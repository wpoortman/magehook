<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Block\Adminhtml\Webhook\Edit;

use Exception;
use MageHook\Hook\Block\Adminhtml\Webhook\GenericButton;

/**
 * Class DeleteButton
 *
 * @package MageHook\Hook\Block\Adminhtml\Webhook\Edit
 */
class DeleteButton extends GenericButton
{
    /**
     * @return array
     * @throws Exception
     */
    public function getButtonData(): array
    {
        $data = [];

        if ($this->getWebhookId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this hook?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', [
            'id' => $this->getWebhookId()
        ]);
    }
}
