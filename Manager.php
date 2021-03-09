<?php
/**
 * @author Willem Poortman <willem@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook;

use Exception;
use MageHook\Hook\Helper\Config\Data as ConfigHelper;
use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Registry\DispatchTimestamp as DispatchTimestampRegistry;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\InvokerInterface;
use Magento\Framework\Event\WrapperFactory;
use Magento\Framework\EventFactory;
use Magento\Framework\Profiler;
use Psr\Log\LoggerInterface;

/**
 * Class Manager
 *
 * @package MageHook\Hook
 */
class Manager implements ManagerInterface
{
    /** @var InvokerInterface $invoker */
    protected $invoker;

    /** @var EventFactory $eventFactory */
    protected $eventFactory;

    /** @var DataObjectFactory $dataObjectFactory */
    protected $dataObjectFactory;

    /** @var WrapperFactory $wrapperFactory */
    protected $wrapperFactory;

    /** @var ConfigHelper $configHelper */
    protected $configHelper;

    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /** @var DispatchTimestampRegistry $dispatchTimestampRegistry */
    protected $dispatchTimestampRegistry;

    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /**
     * Manager constructor.
     *
     * @param InvokerInterface          $invoker
     * @param EventFactory              $eventFactory
     * @param DataObjectFactory         $dataObjectFactory
     * @param WrapperFactory            $wrapperFactory
     * @param ConfigHelper              $configHelper
     * @param EventsHelper              $eventsHelper
     * @param DispatchTimestampRegistry $dispatchTimestampRegistry
     * @param LoggerInterface           $loggerInterface
     */
    public function __construct(
        InvokerInterface $invoker,
        EventFactory $eventFactory,
        DataObjectFactory $dataObjectFactory,
        WrapperFactory $wrapperFactory,
        ConfigHelper $configHelper,
        EventsHelper $eventsHelper,
        DispatchTimestampRegistry $dispatchTimestampRegistry,
        LoggerInterface $loggerInterface
    ) {
        $this->invoker = $invoker;
        $this->eventFactory = $eventFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->wrapperFactory = $wrapperFactory;
        $this->configHelper = $configHelper;
        $this->eventsHelper = $eventsHelper;
        $this->dispatchTimestampRegistry = $dispatchTimestampRegistry;
        $this->loggerInterface = $loggerInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch($event, $data, $metadata = []): void
    {
        if (!$this->configHelper->isActive()) {
            return;
        }

        $this->dispatchTimestampRegistry->set();
        $eventName = mb_strtolower($event);

        Profiler::start('WEBHOOK:' . $event, [
            'group' => 'WEBHOOK', 'name'  => $event,
        ]);

        $searchEvent = $this->eventsHelper->getByEvent($eventName);

        if ($searchEvent) {
            $hook = $this->eventFactory->create(['converter_data' => $data]);
            $hook->setName($event);

            $wrapper = $this->wrapperFactory->create();
            $wrapper->setData([
                self::WRAPPER_DK_EVENT    => $searchEvent,
                self::WRAPPER_DK_WEBHOOK  => $hook,
                self::WRAPPER_DK_BODY     => $data,
                self::WRAPPER_DK_RESOURCE => $data,
                self::WRAPPER_DK_METADATA => $metadata
            ]);

            Profiler::start('WEBHOOK:' . $searchEvent[EventsHelper::EVENT_NAME]);

            try {
                $this->invoker->dispatch($searchEvent, $wrapper);
            } catch (Exception $exception) {
                $this->loggerInterface->critical($exception->getMessage());
            }
//Warning: Use of undefined constant OperationStatusValidator - assumed 'OperationStatusValidator' (this will throw an Error in a future version of PHP) in /var/www/html/vendor/wpoortman/magehook/Model/Queue/Message.php on line 40

            Profiler::stop('WEBHOOK:' . $searchEvent[EventsHelper::EVENT_NAME]);
        }

        Profiler::stop('WEBHOOK:' . $event);
    }

    /**
     * {@inheritDoc}
     */
    public function fire($event, $data, $metadata = []): void
    {
        $this->dispatch($event, $data, $metadata);
    }
}
