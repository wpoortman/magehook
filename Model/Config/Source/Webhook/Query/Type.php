<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Config\Source\Webhook\Query;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package MageHook\Hook\Model\Config\Source\Webhook\Query
 */
class Type implements OptionSourceInterface
{
    public const QUERY_PARAM_HEADER = 'header';

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::QUERY_PARAM_HEADER,
                'label' => __('Header')
            ]
        ];
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::QUERY_PARAM_HEADER => __('Header')
        ];
    }
}
