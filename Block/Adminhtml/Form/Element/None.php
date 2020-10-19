<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Block\Adminhtml\Form\Element;

use Magento\Backend\Block\Template as BackendBlockTemplate;

/**
 * Class None
 *
 * @package MageHook\Hook\Block\Adminhtml\Form\Element
 */
class None extends BackendBlockTemplate
{
    /** @var string $_template */
    protected $_template = 'MageHook_Hook::form/element/none.phtml';
}
