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

/**
 * Class BackButton
 *
 * @package MageHook\Hook\Block\Adminhtml\Webhook
 */
class BackButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => \sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button.
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/');
    }
}
