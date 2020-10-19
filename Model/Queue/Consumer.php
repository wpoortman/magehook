<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Queue;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;
use MageHook\Hook\Api\WebhookRepositoryInterface;
use MageHook\Hook\Model\Queue\Consumer\TypeInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Consumer
 *
 * @package MageHook\Hook\Model\ResourceModel\HttpRequest
 */
class Consumer
{
    /** @var HttpClient $httpClient */
    protected $httpClient;

    /** @var WebhookRepositoryInterface $webhookRepositoryInterface */
    protected $webhookRepositoryInterface;

    /** @var ConsumerTypeList $consumerTypeList */
    protected $consumerTypeList;

    /** @var ManagerInterface $eventManagerInterface */
    protected $eventManagerInterface;

    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /** @var SerializerInterface $serializerInterface */
    protected $serializerInterface;

    /**
     * Consumer constructor.
     *
     * @param HttpClient                 $httpClient
     * @param WebhookRepositoryInterface $webhookRepositoryInterface
     * @param ConsumerTypeList           $consumerTypeList
     * @param EventManagerInterface      $eventManagerInterface
     * @param LoggerInterface            $loggerInterface
     * @param SerializerInterface        $serializerInterface
     */
    public function __construct(
        HttpClient $httpClient,
        WebhookRepositoryInterface $webhookRepositoryInterface,
        ConsumerTypeList $consumerTypeList,
        EventManagerInterface $eventManagerInterface,
        LoggerInterface $loggerInterface,
        SerializerInterface $serializerInterface
    ) {
        $this->httpClient = $httpClient;
        $this->webhookRepositoryInterface = $webhookRepositoryInterface;
        $this->consumerTypeList = $consumerTypeList;
        $this->eventManagerInterface = $eventManagerInterface;
        $this->loggerInterface = $loggerInterface;
        $this->serializerInterface = $serializerInterface;
    }

    /**
     * Process webhook HTTP request.
     *
     * @param OperationMessageInterface $operationMessage
     *
     * @return $this
     */
    public function processOperations(OperationMessageInterface $operationMessage): self
    {
        try {
            $type = $this->getType($operationMessage);

            try {
                $type->request(
                    $type->getMethod(),
                    $type->getOperation()->getUri(),
                    [
                        RequestOptions::JSON    => $this->serializerInterface->unserialize($type->getOperation()->getBody()),
                        RequestOptions::HEADERS => $type->getHook()->getHeaderUriParams()
                    ]
                );

                $this->eventManagerInterface->dispatch('webhook_operation_consume_after', [
                    'operation_message' => $operationMessage,
                    'type'              => $type
                ]);
            } catch (Exception $exception) {
                $this->eventManagerInterface->dispatch('webhook_operation_consume_exception', [
                    'operation_message' => $operationMessage,
                    'exception'         => $exception,
                    'type'              => $type
                ]);
            }
        } catch (NoSuchEntityException $exception) {
            $this->loggerInterface->critical($exception->getMessage());
        }

        return $this;
    }

    /**
     * Get Consumer type.
     *
     * @param OperationMessageInterface $operationMessage
     *
     * @return TypeInterface
     * @throws NoSuchEntityException
     */
    private function getType(OperationMessageInterface $operationMessage): TypeInterface
    {
        $type = $this->consumerTypeList->getType($operationMessage->getType());
        $hook = $this->webhookRepositoryInterface->get($operationMessage->getId());

        $type->setOperation($operationMessage);
        $type->setHook($hook);

        return $type;
    }
}
