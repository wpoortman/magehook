<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Block\Adminhtml\Webhook\Start;

use MageHook\Hook\Block\Adminhtml\Webhook\Edit\SaveButton;

/**
 * Class SaveAndContinueButton
 *
 * @package MageHook\Hook\Block\Adminhtml\Webhook\Start
 */
class SaveAndContinueButton extends SaveButton
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $parent = parent::getButtonData();
        $parent['label'] = __('Continue');

        return $parent;
    }
}
