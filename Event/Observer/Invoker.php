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
use MageHook\Hook\Event\Observer\Dispatcher as ObserverDispatcher;
use MageHook\Hook\Exception\ConverterException;
use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Model\ConverterFactory;
use Magento\Framework\Event\InvokerInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverFactory;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Class Invoker
 *
 * @package MageHook\Hook\Event\Observer
 */
class Invoker implements InvokerInterface
{
    /** @var ObserverFactory $observerFactory */
    protected $observerFactory;

    /** @var EventManagerInterface $eventManagerInterface */
    protected $eventManagerInterface;

    /** @var ServiceOutputProcessor $serviceOutputProcessor */
    protected $serviceOutputProcessor;

    /** @var ConverterFactory $converterFactory */
    protected $converterFactory;

    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * Invoker constructor.
     *
     * @param ObserverFactory $observerFactory
     * @param EventManagerInterface $eventManagerInterface
     * @param ServiceOutputProcessor $serviceOutputProcessor
     * @param ConverterFactory $converterFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ObserverFactory $observerFactory,
        EventManagerInterface $eventManagerInterface,
        ServiceOutputProcessor $serviceOutputProcessor,
        ConverterFactory $converterFactory,
        LoggerInterface $logger
    ) {
        $this->observerFactory = $observerFactory;
        $this->eventManagerInterface = $eventManagerInterface;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->converterFactory = $converterFactory;
        $this->logger = $logger;
    }

    /**
     * @param array    $configuration
     * @param Observer $observer
     */
    public function dispatch(array $configuration, Observer $observer): void
    {
        if (isset($configuration[EventsHelper::EVENT_ATTRIBUTE_DISABLED])
            && true === $configuration[EventsHelper::EVENT_ATTRIBUTE_DISABLED]) {
            return;
        }

        /** @var ObserverDispatcher $object */
        $object = $this->observerFactory->create(ObserverDispatcher::class);

        if (isset($configuration[EventsHelper::EVENT_ATTRIBUTE_SERVICE_CLASS])) {
            try {
                $this->processWithService($configuration[EventsHelper::EVENT_ATTRIBUTE_SERVICE_CLASS], $observer);
            } catch (Exception $exception) {
                $this->logger->critical('Data service processor failed');
            }
        }
        if (isset($configuration[EventsHelper::EVENT_ATTRIBUTE_CONVERTER_CLASS])) {
            try {
                $this->processWithConverter($configuration[EventsHelper::EVENT_ATTRIBUTE_CONVERTER_CLASS], $observer);
            } catch (ConverterException $exception) {
                $this->logger->critical('Data converter failed');
            }
        }

        if (\is_array($observer->getBody())) {
            $this->eventManagerInterface->dispatch('hook_dispatch_before', [
                'observer' => $observer, 'config' => $configuration
            ]);

            $object->execute($observer);
        }
    }

    /**
     * @param string $serviceClass
     * @param        $observer
     */
    protected function processWithService(string $serviceClass, &$observer): void
    {
        $observer->setBody($this->serviceOutputProcessor->convertValue($observer->getBody(), $serviceClass));
    }

    /**
     * @param string $converterClass
     * @param        $observer
     *
     * @throws ConverterException
     */
    protected function processWithConverter(string $converterClass, &$observer): void
    {
        try {
            $converter = $this->converterFactory->create($converterClass);
        } catch (RuntimeException $exception) {
            throw new ConverterException(__($exception->getMessage()));
        }

        $observer->setBody($converter->convert($observer->getBody()));
    }
}
