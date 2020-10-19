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

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Start
 *
 * @package MageHook\Hook\Controller\Adminhtml\Grid
 */
class Start extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageHook_Hook::default';

    public const BUTTON_LABEL_NEW  = 'New';

    /**
     * @return Page
     */
    protected function _initAction(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage
            ->addBreadcrumb(__('MageHook'), __('MageHook'))
            ->addBreadcrumb(__('Hook'), __('Hook'));

        return $resultPage;
    }

    /**
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            __(self::BUTTON_LABEL_NEW),
            __(self::BUTTON_LABEL_NEW)
        );

        return $resultPage;
    }
}
