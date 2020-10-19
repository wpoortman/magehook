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

use Magento\AsynchronousOperations\Model\Operation;
use Magento\Framework\Serialize\SerializerInterface;
use function is_array;

/**
 * Class Message
 *
 * @package MageHook\Hook\Model\Queue
 */
class Message extends Operation implements OperationMessageInterface
{
    /** @var SerializerInterface $serializerInterface */
    protected $serializerInterface;

    /**
     * Message constructor.
     *
     * @param SerializerInterface $serializerInterface
     * @param array               $data
     */
    public function __construct(
        SerializerInterface $serializerInterface,
        array $data = []
    ) {
        parent::__construct($data);

        $this->serializerInterface = $serializerInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getData(self::KEY_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        return $this->setData(self::KEY_ID, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return $this->getData(self::SERIALIZED_DATA);
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        return $this->getData(self::KEY_URI);
    }

    /**
     * {@inheritDoc}
     */
    public function setUri($uri)
    {
        return $this->setData(self::KEY_URI, $uri);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvent()
    {
        return $this->getData(self::KEY_EVENT);
    }

    /**
     * {@inheritDoc}
     */
    public function setEvent($event)
    {
        return $this->setData(self::KEY_EVENT, $event);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->getData(self::KEY_TYPE);
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaData($asArray = false)
    {
        $data = $this->getData(self::KEY_METADATA);
        return $asArray ? $this->serializerInterface->unserialize($data) : $data;
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaData($data)
    {
        if (is_array($data)) {
            $data = $this->serializerInterface->serialize($data);
        }

        return $this->setData(self::KEY_METADATA, $data);
    }
}
