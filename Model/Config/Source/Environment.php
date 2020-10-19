<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Environment
 *
 * @package MageHook\Hook\Model\Config\Source
 */
class Environment implements OptionSourceInterface
{
    public const ENVIRONMENT_DEVELOPMENT = 'development';
    public const ENVIRONMENT_PRODUCTION  = 'production';
    public const ENVIRONMENT_STAGING     = 'staging';
    public const ENVIRONMENT_TEST        = 'test';
    public const ENVIRONMENT_UNKNOWN     = 'unknown';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ENVIRONMENT_UNKNOWN,     'label' => __('Unknown')],
            ['value' => self::ENVIRONMENT_DEVELOPMENT, 'label' => __('Development')],
            ['value' => self::ENVIRONMENT_PRODUCTION,  'label' => __('Production')],
            ['value' => self::ENVIRONMENT_STAGING,     'label' => __('Staging')],
            ['value' => self::ENVIRONMENT_TEST,        'label' => __('Test')],
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
            self::ENVIRONMENT_UNKNOWN     => __('Unknown'),
            self::ENVIRONMENT_DEVELOPMENT => __('Development'),
            self::ENVIRONMENT_PRODUCTION  => __('Production'),
            self::ENVIRONMENT_STAGING     => __('Staging'),
            self::ENVIRONMENT_TEST        => __('Test'),
        ];
    }
}
