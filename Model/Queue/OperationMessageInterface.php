<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Model\Queue;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;

/**
 * Interface OperationMessageInterface
 *
 * @package MageHook\Hook\Model\Queue
 */
interface OperationMessageInterface extends OperationInterface
{
    public const TOPIC_OPERATION_NAME = 'async.webhook.http-request';

    public const KEY_ID        = 'id';
    public const KEY_URI       = 'uri';
    public const KEY_EVENT     = 'event';
    public const KEY_TYPE      = 'type';
    public const KEY_BULK_UUID = self::BULK_ID;
    public const KEY_METADATA  = 'meta_data';

    public const PUBLIC_DATA_KEY = 'public';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return OperationMessageInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getUri();

    /**
     * @param string $uri
     * @return OperationMessageInterface
     */
    public function setUri($uri);

    /**
     * @return string
     */
    public function getEvent();

    /**
     * @param string $event
     * @return OperationMessageInterface
     */
    public function setEvent($event);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $event
     * @return OperationMessageInterface
     */
    public function setType($event);

    /**
     * @param bool $asArray
     *
     * @return mixed
     */
    public function getMetaData($asArray = false);

    /**
     * @param array|string $data
     * @return OperationMessageInterface
     */
    public function setMetaData($data);
}
