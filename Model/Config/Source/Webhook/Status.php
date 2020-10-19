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

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Framework\Data\OptionSourceInterface;
use function ucfirst;

/**
 * Class Status
 *
 * @package MageHook\Hook\Model\Config\Source\Webhook
 */
class Status implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => WebhookInterface::STATUS_ACTIVE,
                'label' => __(ucfirst('Enable'))
            ],
            [
                'value' => WebhookInterface::STATUS_CONCEPT,
                'label' => __(ucfirst('Concept'))
            ],
            [
                'value' => WebhookInterface::STATUS_INACTIVE,
                'label' => __(ucfirst('Disable'))
            ]
        ];
    }
}
