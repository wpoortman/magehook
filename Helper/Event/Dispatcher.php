<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Helper\Event;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Model\Queue\OperationMessageInterface;
use MageHook\Hook\Registry\DispatchTimestamp as DispatchTimestampRegistry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Dispatcher
 *
 * @package MageHook\Hook\Helper\Event
 */
class Dispatcher
{
    public const MESSAGE_TOPIC_NAME = 'async.webhook.http-request';

    /** @var DispatchTimestampRegistry $dispatchTimestampRegistry */
    protected $dispatchTimestampRegistry;

    /** @var SerializerInterface $serializerInterface */
    protected $serializerInterface;

    /**
     * Dispatcher constructor.
     *
     * @param DispatchTimestampRegistry $dispatchTimestampRegistry
     * @param SerializerInterface       $serializerInterface
     */
    public function __construct(
        DispatchTimestampRegistry $dispatchTimestampRegistry,
        SerializerInterface $serializerInterface
    ) {
        $this->dispatchTimestampRegistry = $dispatchTimestampRegistry;
        $this->serializerInterface = $serializerInterface;
    }

    /**
     * @param WebhookInterface $hook
     * @param array $body
     * @param array $metadata
     * @param array $event
     * @param string $uuid
     * @return array
     */
    public function generateOperationMessage(
        WebhookInterface $hook,
        array $body,
        array $metadata,
        array $event,
        string $uuid
    ): array {
        // Generate data just to signal (overwrites body)
        $body = $hook->onlySignal() ? $this->generateSignal() : $body;
        $body = $this->generatePayloadWrapper($body, $metadata, $event, $uuid);

        return [
            'id'              => $hook->getId(),
            'serialized_data' => $this->serializerInterface->serialize($body),
            'meta_data'       => $this->serializerInterface->serialize($metadata),
            'uri'             => $hook->getUrl(),
            'event'           => $event['event'],
            'type'            => $event['type'],
            'status'          => 2,
            'bulk_uuid'       => $uuid,
            'topic_name'      => self::MESSAGE_TOPIC_NAME
        ];
    }

    /**
     * Generate a signal HTTP (POST) body.
     *
     * @return array
     */
    public function generateSignal(): array
    {
        return ['ping' => true];
    }

    /**
     * Generate final payload.
     *
     * @param array  $body
     * @param array  $metadata
     * @param array  $event
     * @param string $uuid
     *
     * @return array
     */
    public function generatePayloadWrapper(array $body, array $metadata, array $event, string $uuid): array
    {
        return [
            'timestamp' => $this->dispatchTimestampRegistry->get(),
            'data'      => $body,
            'meta_data' => $metadata[OperationMessageInterface::PUBLIC_DATA_KEY] ?? [],
            'event'     => $event['event'] ?? 'unknown',
            'uuid'      => $uuid
        ];
    }
}
