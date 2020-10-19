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

use MageHook\Hook\Helper\Config\Data as ConfigDataHelper;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Index
 *
 * @package MageHook\Hook\Controller\Adminhtml\Grid
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageHook_Hook::default';

    /** @var ConfigDataHelper $configDataHelper */
    protected $configDataHelper;

    /**
     * Index constructor.
     *
     * @param Action\Context   $context
     * @param ConfigDataHelper $configDataHelper
     */
    public function __construct(
        Action\Context $context,
        ConfigDataHelper $configDataHelper
    ) {
        parent::__construct($context);

        $this->configDataHelper = $configDataHelper;
    }

    /**
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('MageHook_Hook::main')
            ->getConfig()
            ->getTitle()
            ->prepend(__('Hook Management'));

        if (!$this->configDataHelper->isActive()) {
            $this->getMessageManager()->addNoticeMessage(
                'MageHook is currently inactive. You can enable webhook dispatching by navigating to Stores > Settings > Configuration > Services > MageHook > General'
            );
        }

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('MageHook_Hook::main');
    }
}
