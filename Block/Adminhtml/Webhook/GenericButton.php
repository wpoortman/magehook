<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Block\Adminhtml\Webhook;

use MageHook\Hook\Model\WebhookRepository;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class GenericButton
 *
 * @package MageHook\Hook\Block\Adminhtml\Webhook
 */
abstract class GenericButton implements ButtonProviderInterface
{
    /** @var Context $context */
    protected $context;

    /** @var WebhookRepository $webhookRepository */
    protected $webhookRepository;

    /**
     * @param Context $context
     * @param WebhookRepository $webhookRepository
     */
    public function __construct(
        Context $context,
        WebhookRepository $webhookRepository
    ) {
        $this->context = $context;
        $this->webhookRepository = $webhookRepository;
    }

    /**
     * Get webhook ID.
     *
     * @return null|string|int
     * @throws \Exception
     */
    public function getWebhookId()
    {
        try {
            $id = $this->context->getRequest()->getParam('id');

            if ($id) {
                return $this->webhookRepository->get($id)->getId();
            }
        } catch (NoSuchEntityException $exception) {
            // ..
        }

        return null;
    }

    /**
     * Generate url by route and parameters.
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
