<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Test\Unit;

use MageHook\Hook\Event\ConfigInterface;
use MageHook\Hook\Event\Observer\Invoker;
use MageHook\Hook\Helper\Config\Data as ConfigDataHelper;
use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Manager as WebhookEventManager;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\WrapperFactory;
use Magento\Framework\EventFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ManagerTest
 *
 * @package MageHook\Hook\Test\Unit
 */
class ManagerTest extends TestCase
{
    /** @var MockObject $invokerMock */
    protected $invokerMock;

    /** @var MockObject $eventFactory */
    protected $eventFactory;

    /** @var MockObject $event */
    protected $event;

    /** @var MockObject $wrapperFactory */
    protected $wrapperFactory;

    /** @var MockObject $observer */
    protected $observer;

    /** @var MockObject $eventConfigMock */
    protected $eventConfigMock;

    /** @var object $webhookEventManager */
    protected $webhookEventManager;

    /** @var ObjectManagerHelper $objectManagerHelper */
    protected $objectManagerHelper;

    /** @var MockObject $configHelperMock */
    protected $configHelperMock;

    /** @var MockObject $eventsHelper */
    protected $eventsHelper;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->invokerMock      = $this->createMock(Invoker::class);
        $this->eventConfigMock  = $this->createMock(ConfigInterface::class);
        $this->configHelperMock = $this->createMock(ConfigDataHelper::class);
        $this->eventsHelper     = $this->createMock(EventsHelper::class);
        $this->eventFactory     = $this->createMock(EventFactory::class);
        $this->wrapperFactory   = $this->createMock(WrapperFactory::class);

        // Set module as active by default
        $this->configHelperMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $this->webhookEventManager = $this->objectManagerHelper->getObject(
            WebhookEventManager::class,
            [
                'invoker'        => $this->invokerMock,
                'eventConfig'    => $this->eventConfigMock,
                'configHelper'   => $this->configHelperMock,
                'eventsHelper'   => $this->eventsHelper,
                'eventFactory'   => $this->eventFactory,
                'wrapperFactory' => $this->wrapperFactory
            ]
        );
    }

    public function testDispatch(): void
    {
        $this->eventsHelper->expects($this->once())
            ->method('getByEvent')
            ->willReturn([
                EventsHelper::EVENT_ATTRIBUTE_PURPOSE         => null,
                EventsHelper::EVENT_ATTRIBUTE_SERVICE_CLASS   => null,
                EventsHelper::EVENT_ATTRIBUTE_CONVERTER_CLASS => null,
                EventsHelper::EVENT_ATTRIBUTE_VALIDATOR_CLASS => null,
                EventsHelper::EVENT_ATTRIBUTE_TYPE            => 'default',
                EventsHelper::EVENT_ATTRIBUTE_REQUEST         => 'sync',
                EventsHelper::EVENT_ATTRIBUTE_LIST            => 'adminhtml',
                EventsHelper::EVENT_NAME                      => 'test_ping',
                EventsHelper::EVENT_ATTRIBUTE_TITLE           => 'Test Ping',
                EventsHelper::EVENT_ATTRIBUTE_GROUP           => 'Test',
                EventsHelper::EVENT_ELEMENT_OBSERVER          => 'test_ping'
            ]);

        $this->eventFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->createMock(Event::class));

        $this->wrapperFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->createMock(Observer::class));

        $this->webhookEventManager->dispatch('test_ping', ['test'], ['test', 'public' => 'test']);
    }
}
