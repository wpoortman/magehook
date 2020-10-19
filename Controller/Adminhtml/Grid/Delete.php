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
use MageHook\Hook\Model\WebhookRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Delete
 *
 * @package MageHook\Hook\Controller\Adminhtml\Grid
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageHook_Hook::default';

    /** @var Registry $coreRegistry */
    protected $coreRegistry;

    /** @var DataPersistorInterface  */
    protected $dataPersistor;

    /** @var WebhookRepository  */
    protected $webhookRepository;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     * @param WebhookRepository $webhookRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        Registry $registry,
        WebhookRepository $webhookRepository,
        SerializerInterface $serializer
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $registry;
        $this->webhookRepository = $webhookRepository;
        $this->serializer = $serializer;

        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var null|string $id */
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $webhook = $this->webhookRepository->get($id);

                $this->webhookRepository->delete($webhook);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                return $resultRedirect->setPath('*/*/edit', [
                    WebhookInterface::ID => $id
                ]);
            }

            // Default success message
            $successMessage = 'Hook has been deleted successfully';

            if ($webhook->getId()) {
                if ($webhook->isConcept()) {
                    $successMessage = __('Concept webhook has been deleted');
                } elseif (!empty($webhook->getUrl())) {
                    $successMessage = __('The webhook pointed towards %1 has been deleted and cant be dispatched any longer.', $webhook->getUrl());
                }
            }

            $this->messageManager->addSuccessMessage($successMessage);
            return $resultRedirect->setPath('*/*/');
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a web hook to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
