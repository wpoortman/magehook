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

use MageHook\Hook\Helper\Event\Dispatcher as EventDispatcherHelper;
use MageHook\Hook\ManagerInterface as HookManagerInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Ping
 *
 * @package MageHook\Hook\Controller\Adminhtml\Grid
 */
class Ping extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageHook_Hook::default';

    /** @var HookManagerInterface $hookEventsManagerInterface */
    protected $hookEventsManagerInterface;

    /** @var EventDispatcherHelper $eventDispatcherHelper */
    protected $eventDispatcherHelper;

    /**
     * Ping constructor.
     *
     * @param Action\Context $context
     * @param HookManagerInterface $hookEventsManagerInterface
     * @param EventDispatcherHelper $eventDispatcherHelper
     */
    public function __construct(
        Action\Context $context,
        HookManagerInterface $hookEventsManagerInterface,
        EventDispatcherHelper $eventDispatcherHelper
    ) {
        parent::__construct($context);

        $this->hookEventsManagerInterface = $hookEventsManagerInterface;
        $this->eventDispatcherHelper = $eventDispatcherHelper;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var null|string $event */
        $event = $this->getRequest()->getParam('event');
        /** @var ResultInterface $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($event) {
            try {
                // Dynamically dispatch a event with just signal data
                $this->hookEventsManagerInterface->dispatch($event, $this->eventDispatcherHelper->generateSignal());

                $this->messageManager->addSuccessMessage(
                    __('A test ping for event "%1" has been dispatched successfully. Please be aware of the fact that
                    this can be an asynchronous action which can take longer to send and the normal data has been
                    overwritten with signal data', $event)
                );
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }

        return $result->setPath('*/*/index');
    }
}
