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

use MageHook\Hook\Block\Adminhtml\Webhook\GenericButton;

/**
 * Class SaveButton
 *
 * @package MageHook\Hook\Block\Adminhtml\Webhook\Edit
 */
class SaveButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'save'
                    ]
                ],
                'form-role' => 'save'
            ],
            'sort_order' => 90,
        ];
    }
}
