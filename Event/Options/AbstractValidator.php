<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Event\Options;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\ManagerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as  DataObjectFactory;
use Magento\Framework\Event\Observer as ObserverEvent;

/**
 * Interface ValidatorInterface
 *
 * @package MageHook\Hook\Event\Options
 *
 * @api
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /** @var WebhookInterface $webhookInterface */
    protected $webhookInterface;

    /** @var ObserverEvent $observerEvent */
    protected $observerEvent;

    /** @var DataObjectFactory $dataObjectFactory */
    protected $dataObjectFactory;

    /** @var DataObjectFactory $optionsObjectFactory */
    protected $optionsObjectFactory;

    /** @var array|DataObject $data */
    private $data;

    /** @var DataObject $options */
    private $options;

    /**
     * AbstractValidator constructor.
     *
     * @param WebhookInterface  $webhookInterface
     * @param ObserverEvent     $observerEvent
     * @param DataObjectFactory $dataObjectFactory
     * @param DataObjectFactory $optionsObjectFactory
     * @param array             $data
     */
    public function __construct(
        WebhookInterface $webhookInterface,
        ObserverEvent $observerEvent,
        DataObjectFactory $dataObjectFactory,
        DataObjectFactory $optionsObjectFactory,
        array $data = []
    ) {
        $this->webhookInterface = $webhookInterface;
        $this->observerEvent = $observerEvent;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->optionsObjectFactory = $optionsObjectFactory;
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): DataObject
    {
        if (is_array($this->data)) {
            /** @var DataObject data */
            $this->data = $this->dataObjectFactory->create(
                array_merge($this->data, $this->observerEvent->getData(ManagerInterface::WRAPPER_DK_METADATA))
            );
        }

        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): DataObject
    {
        if ($this->options === null) {
            $this->options = $this->optionsObjectFactory->create(
                $this->webhookInterface->getCustomOptions()
            );
        }

        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function getEvent(): array
    {
        return $this->observerEvent->getData(ManagerInterface::WRAPPER_DK_EVENT);
    }

    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        return $this->observerEvent->getData(ManagerInterface::WRAPPER_DK_RESOURCE);
    }

    /**
     * {@inheritDoc}
     */
    public function getBody(): array
    {
        return $this->observerEvent->getData(ManagerInterface::WRAPPER_DK_BODY);
    }
}
