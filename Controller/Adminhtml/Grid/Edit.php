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
use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Model\WebhookFactory;
use MageHook\Hook\Model\WebhookRepository;
use MageHook\Hook\Registry\ActionHookEventSelect as ActionHookEventSelectRegistry;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Edit
 *
 * @package MageHook\Hook\Controller\Adminhtml\Grid
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageHook_Hook::default';

    /** @var WebhookFactory $webhookFactory */
    protected $webhookFactory;

    /** @var WebhookRepository $webhookRepository */
    protected $webhookRepository;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var ActionHookEventSelectRegistry $actionHookEventSelectRegistry */
    protected $actionHookEventSelectRegistry;

    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /**
     * Edit constructor.
     *
     * @param Context                       $context
     * @param WebhookFactory                $webhookFactory
     * @param WebhookRepository             $webhookRepository
     * @param SerializerInterface           $serializer
     * @param ActionHookEventSelectRegistry $actionHookEventSelectRegistry
     * @param EventsHelper                  $eventsHelper
     */
    public function __construct(
        Context $context,
        WebhookFactory $webhookFactory,
        WebhookRepository $webhookRepository,
        SerializerInterface $serializer,
        ActionHookEventSelectRegistry $actionHookEventSelectRegistry,
        EventsHelper $eventsHelper
    ) {
        $this->webhookFactory = $webhookFactory;
        $this->webhookRepository = $webhookRepository;
        $this->serializer = $serializer;
        $this->actionHookEventSelectRegistry = $actionHookEventSelectRegistry;
        $this->eventsHelper = $eventsHelper;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage
            ->addBreadcrumb(__('MageHook'), __('MageHook'))
            ->addBreadcrumb(__('Hook'), __('Hook'));

        return $resultPage;
    }

    /**
     * @return Page|Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var null|string $id */
        $id = $this->getRequest()->getParam(WebhookInterface::ID);
        /** @var null|string $event */
        $event = $this->getRequest()->getParam(WebhookInterface::EVENT);
        /** @var WebhookInterface $webhook */
        $webhook = $this->webhookFactory->create();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($id) {
            try {
                $webhook = $this->webhookRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                return $resultRedirect->setPath('*/*/edit', [
                    WebhookInterface::ID => $id
                ]);
            }

            if (!$webhook->getId()) {
                $this->messageManager->addErrorMessage(
                    __('This web hook does no longer exists.')
                );

                return $resultRedirect->setPath('*/*/index');
            }
        }

        try {
            if ($id && $webhook->getEvent()) {
                if ($webhook->isConcept()) {
                    $this->messageManager->addNoticeMessage(
                        __('You are currently in a concept phase. Finalize the form, change the status and save')
                    );
                }

                $this->actionHookEventSelectRegistry->set($webhook->getEvent());
            }
        } catch (AlreadyExistsException $exception) {
            $this->messageManager->addErrorMessage(
                'Could not instantiate the event options'
            );
        }

        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $webhook->isConcept() ? __('Concept') : __('Edit'),
            $webhook->isConcept() ? __('Concept') : __('Edit')
        );

        if ($webhook->getEvent()) {
            $resultPage->getConfig()
                ->getTitle()
                ->prepend(
                    $this->eventsHelper->getEventLabel(
                        $webhook->getEvent()
                    )
                );
        }

        return $resultPage;
    }
}
