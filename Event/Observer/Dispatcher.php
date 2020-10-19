<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Event\Observer;

use Exception;
use InvalidArgumentException;
use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Api\WebhookRepositoryInterface;
use MageHook\Hook\Event\Options\ValidatorFactory as OptionsValidatorFactory;
use MageHook\Hook\Helper\Event\Dispatcher as DispatcherHelper;
use MageHook\Hook\Helper\Events;
use MageHook\Hook\ManagerInterface;
use MageHook\Hook\Model\Queue\Consumer;
use MageHook\Hook\Model\Queue\OperationMessageInterfaceFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Event\Observer as FrameworkObserver;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use function count;

/**
 * Class Dispatcher
 *
 * @package MageHook\Hook\Event\Observer
 */
class Dispatcher implements ObserverInterface
{
    /** @var WebhookRepositoryInterface $webhookRepository */
    protected $webhookRepository;

    /** @var BulkManagementInterface $bulkManagerInterface */
    protected $bulkManagerInterface;

    /** @var IdentityGeneratorInterface $identityServiceInterface */
    protected $identityServiceInterface;

    /** @var OperationMessageInterfaceFactory $operationFactory */
    protected $operationFactory;

    /** @var EventManagerInterface $eventManagerInterface */
    protected $eventManagerInterface;

    /** @var DispatcherHelper $dispatcherHelper */
    protected $dispatcherHelper;

    /** @var Consumer $consumer */
    protected $consumer;

    /** @var OptionsValidatorFactory $optionsValidatorFactory */
    protected $optionsValidatorFactory;

    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /**
     * Dispatcher constructor.
     *
     * @param WebhookRepositoryInterface       $webhookRepository
     * @param BulkManagementInterface          $bulkManagementInterface
     * @param IdentityGeneratorInterface       $identityServiceInterface
     * @param OperationMessageInterfaceFactory $operationFactory
     * @param EventManagerInterface            $eventManagerInterface
     * @param DispatcherHelper                 $dispatcherHelper
     * @param Consumer                         $consumer
     * @param OptionsValidatorFactory          $optionsValidatorFactory
     * @param LoggerInterface                  $loggerInterface
     */
    public function __construct(
        WebhookRepositoryInterface $webhookRepository,
        BulkManagementInterface $bulkManagementInterface,
        IdentityGeneratorInterface $identityServiceInterface,
        OperationMessageInterfaceFactory $operationFactory,
        EventManagerInterface $eventManagerInterface,
        DispatcherHelper $dispatcherHelper,
        Consumer $consumer,
        OptionsValidatorFactory $optionsValidatorFactory,
        LoggerInterface $loggerInterface
    ) {
        $this->webhookRepository = $webhookRepository;
        $this->bulkManagerInterface = $bulkManagementInterface;
        $this->identityServiceInterface = $identityServiceInterface;
        $this->operationFactory = $operationFactory;
        $this->eventManagerInterface = $eventManagerInterface;
        $this->dispatcherHelper = $dispatcherHelper;
        $this->consumer = $consumer;
        $this->optionsValidatorFactory = $optionsValidatorFactory;
        $this->loggerInterface = $loggerInterface;
    }

    /**
     * @param FrameworkObserver $observer
     */
    public function execute(FrameworkObserver $observer): void
    {
        try {
            $hooks = $this->webhookRepository->getListByName($observer->getWebhook()->getName());
            // Prepare operation messages
            $operations = $this->prepareOperations($hooks, $observer);

            foreach ($operations as $method => $context) {
                $this->{$method}($context);
            }
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

    /**
     * Prepares operations based on their event request method.
     *
     * @param SearchResultsInterface $hooks
     * @param FrameworkObserver      $observer
     *
     * @return array
     */
    public function prepareOperations(SearchResultsInterface $hooks, FrameworkObserver $observer): array
    {
        /** @var array $event */
        $event = $observer->getEvent();

        // Bulk operation messages (required)
        $operations = [
            $event['request'] => [
                'uuid' => $this->identityServiceInterface->generateId(),
                'operations' => []
            ]
        ];

        if ($hooks->getTotalCount() !== 0) {
            /** @var WebhookInterface $hook */
            foreach ($hooks->getItems() as $hook) {
                if ($this->validate($hook, $observer) === false) {
                    continue;
                }

                // Add new operation to the stack
                $operations[$event['request']]['operations'][] = $this->operationFactory->create([
                    'data' => $this->dispatcherHelper->generateOperationMessage(
                        $hook,
                        $observer->getData(ManagerInterface::WRAPPER_DK_BODY),
                        $observer->getData(ManagerInterface::WRAPPER_DK_METADATA),
                        $event,
                        $operations[$event['request']]['uuid']
                    )
                ]);
            }
        }

        return $operations;
    }

    /**
     * Validate if the hook event can be processed into a operation message.
     *
     * @param WebhookInterface  $hook
     * @param FrameworkObserver $observer
     *
     * @return bool
     */
    public function validate(WebhookInterface $hook, FrameworkObserver $observer): bool
    {
        /** @var array $event */
        $event = $observer->getEvent();

        if (isset($event[Events::EVENT_ATTRIBUTE_VALIDATOR_CLASS])) {
            $class = $event[Events::EVENT_ATTRIBUTE_VALIDATOR_CLASS];
            $validator = $this->optionsValidatorFactory->create($hook, $observer, $class);

            if ($validator) {
                // Distillate validation methods.
                $methods = preg_grep('/^validate/', get_class_methods($validator));

                try {
                    foreach ($methods as $method) {
                        if ($validator->{$method}() !== true) {
                            return false;
                        }
                    }
                } catch (Exception $exception) {
                    $this->loggerInterface->critical($exception->getMessage());
                }
            }
        }

        return true;
    }

    /**
     * Schedules all operations.
     *
     * @param array $context
     *
     * @return bool
     */
    public function async(array $context): bool
    {
        return $this->bulkManagerInterface->scheduleBulk(
            $context['uuid'],
            $context['operations'],
            'Schedules webhook event operations'
        );
    }

    /**
     * Consumes the operations right away instead of using the Message Queue.
     *
     * @todo The HookInterface $hook will be loaded again within the processOperations method and can be seen as overkill
     *
     * @param array $context
     */
    public function sync(array $context): void
    {
        foreach ($context['operations'] as $operation) {
            $this->consumer->processOperations($operation);
        }
    }
}
