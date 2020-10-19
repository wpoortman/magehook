<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Controller\Adminhtml\Grid;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Api\Data\WebhookInterfaceFactory;
use MageHook\Hook\Api\WebhookRepositoryInterface;
use MageHook\Hook\Model\Config\Source\Webhook\DeploymentMode;
use MageHook\Hook\Ui\DataProvider\Form\Webhook as HookFormDataProvider;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 *
 * @package MageHook\Hook\Controller\Adminhtml\Grid
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageHook_Hook::default';

    /** @var DataObjectHelper $dataObjectHelper */
    protected $dataObjectHelper;

    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /** @var WebhookRepositoryInterface $webhookRepositoryInterface */
    protected $webhookRepositoryInterface;

    /** @var WebhookInterfaceFactory $webhookInterfaceFactory */
    protected $webhookInterfaceFactory;

    /**
     * Save constructor.
     *
     * @param Action\Context             $context
     * @param DataObjectHelper           $dataObjectHelper
     * @param LoggerInterface            $loggerInterface
     * @param WebhookRepositoryInterface $webhookRepositoryInterface
     * @param WebhookInterfaceFactory    $webhookInterfaceFactory
     */
    public function __construct(
        Action\Context $context,
        DataObjectHelper $dataObjectHelper,
        LoggerInterface $loggerInterface,
        WebhookRepositoryInterface $webhookRepositoryInterface,
        WebhookInterfaceFactory $webhookInterfaceFactory
    ) {
        parent::__construct($context);

        $this->dataObjectHelper = $dataObjectHelper;
        $this->loggerInterface = $loggerInterface;
        $this->webhookRepositoryInterface = $webhookRepositoryInterface;
        $this->webhookInterfaceFactory = $webhookInterfaceFactory;
    }

    /**
     * Try to save or create hook.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var null|WebhookInterface $hook */
        $hook = null;

        // Get (optional) existing hook id
        $id      = $this->getRequest()->getParam('id');
        // Get the selected webhook event
        $event   = (string)$this->getRequest()->getParam('event');
        // Check if the event came from a concept (start) phase
        $concept = (bool)$this->getRequest()->getParam('is_concept');
        // Get request return method
        $return  = (bool)$this->getRequest()->getParam('back', $concept);

        try {
            $hook = $this->saveHook($id, $event, $concept);

            if (!$hook->isConcept()) {
                $this->messageManager->addSuccessMessage(
                    __('All data has been successfully stored in the database')
                );
            }
        } catch (\Exception $exception) {
            $return = true;
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong. Please try again or contact your system administrator.')
            );
        }

        return $this->getRedirect($hook, $return);
    }

    /**
     * @param WebhookInterface|null $hook
     * @param bool                  $return
     *
     * @return Redirect
     */
    public function getRedirect($hook, bool $return = false): Redirect
    {
        $redirect = $this->resultRedirectFactory->create();

        if ($return && $hook !== null && $hook->getId()) {
            $redirect->setPath('webhook/grid/edit', ['id' => $hook->getId()]);
        } else {
            $redirect->setPath('webhook/grid/index');
        }

        return $redirect;
    }

    /**
     * @param mixed  $id
     * @param string $event
     * @param bool   $concept
     *
     * @return WebhookInterface|null
     */
    public function saveHook($id, string $event, bool $concept = false): ?WebhookInterface
    {
        /** @var null|WebhookInterface $hook */
        $hook = null;
        $data = $concept ? [] : $this->extractRequestData();

        try {
            if ($id !== null) {
                $hook = $this->webhookRepositoryInterface->get($id);
            } else {
                $hook = $this->webhookInterfaceFactory->create();

                if ($concept === true) {
                    $hook->setEvent($event);
                    $hook->setIsConcept();
                    $hook->setDeploymentMode(DeploymentMode::MODE_INDEPENDENT);

                    // ISSUE #29: Fix to have some initial data from the get-go
                    $hook->setCustomOptions([]);
                }
            }

            $this->setRequestData($hook, $data);
            $this->webhookRepositoryInterface->save($hook);
        } catch (CouldNotSaveException $exception) {
            $this->loggerInterface->critical($exception->getMessage());
        } catch (NoSuchEntityException $exception) {
            $this->loggerInterface->critical($exception->getMessage());
        }

        return $hook;
    }

    /**
     * @return array
     */
    public function extractRequestData(): array
    {
        $properties = [
            WebhookInterface::ID,
            WebhookInterface::DEPLOYMENT_MODE,
            WebhookInterface::IS_ACTIVE,
            WebhookInterface::NAME,
            WebhookInterface::EVENT,
            WebhookInterface::URL,
            WebhookInterface::QUERY_DATA,
            WebhookInterface::ONLY_SIGNAL,
            WebhookInterface::CUSTOM_OPTIONS,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ];

        $data = [];

        $request = $this->getRequest()->getParams();
        $this->transformToCustomOptions($request);

        if (\is_array($request)) {
            foreach ($request as $fields) {
                if (!\is_array($fields)) {
                    continue;
                }
                // Aware of the heaviness, functional for now
                $data = \array_merge_recursive($data, $fields);
            }
        }

        return \array_intersect_key($data, \array_flip($properties));
    }

    /**
     * Populate WebhookInterface with request data.
     *
     * @param WebhookInterface $hook
     * @param array            $data
     *
     * @return WebhookInterface
     */
    public function setRequestData(WebhookInterface $hook, array $data): WebhookInterface
    {
        $this->dataObjectHelper->populateWithArray($hook, $data, WebhookInterface::class);
        return $hook;
    }

    /**
     * @param array $request HTTP post request params
     */
    public function transformToCustomOptions(array &$request): void
    {
        $data = $request[HookFormDataProvider::AREA_OPTIONS] ?? [];
        $request[HookFormDataProvider::AREA_OPTIONS][WebhookInterface::CUSTOM_OPTIONS] = $data;
    }
}
