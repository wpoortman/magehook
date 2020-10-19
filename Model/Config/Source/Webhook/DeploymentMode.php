<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Config\Source\Webhook;

use Magento\Framework\App\State;
use Magento\Framework\Data\OptionSourceInterface;
use function array_map;

/**
 * Class DeploymentMode
 *
 * @package MageHook\Hook\Model\Config\Source\Webhook
 */
class DeploymentMode implements OptionSourceInterface
{
    public const MODE_INDEPENDENT = 'independent';
    public const MODE_DEFAULT     = State::MODE_DEFAULT;
    public const MODE_DEVELOPER   = State::MODE_DEVELOPER;
    public const MODE_PRODUCTION  = State::MODE_PRODUCTION;

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::MODE_INDEPENDENT,
                'label' => __(\ucfirst(self::MODE_INDEPENDENT))
            ],
            [
                'value' => self::MODE_DEFAULT,
                'label' => __(\ucfirst(self::MODE_DEFAULT))
            ],
            [
                'value' => self::MODE_DEVELOPER,
                'label' => __(\ucfirst(self::MODE_DEVELOPER))
            ],
            [
                'value' => self::MODE_PRODUCTION,
                'label' => __(\ucfirst(self::MODE_PRODUCTION))
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
        return array_map('ucfirst', [
            self::MODE_INDEPENDENT => __(self::MODE_INDEPENDENT),
            self::MODE_DEFAULT     => __(self::MODE_DEFAULT),
            self::MODE_DEVELOPER   => __(self::MODE_DEVELOPER),
            self::MODE_PRODUCTION  => __(self::MODE_PRODUCTION)
        ]);
    }
}
