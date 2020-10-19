<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Helper\Model;

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Webhook
 *
 * @package MageHook\Hook\Helper\Model
 */
class Webhook
{
    public const DATA_VERSION = '1.0.0';

    /** @var ProductMetadataInterface $productMetaDataInterface */
    protected $productMetaDataInterface;

    /**
     * Webhook constructor.
     *
     * @param ProductMetadataInterface $productMetaDataInterface
     */
    public function __construct(
        ProductMetadataInterface $productMetaDataInterface
    ) {
        $this->productMetaDataInterface = $productMetaDataInterface;
    }

    /**
     * Sets meta data as data comparison material.
     *
     * @param WebhookInterface $webhook
     * @param array            $options
     *
     * @return array
     */
    public function defineCustomOptionsMetaData(WebhookInterface $webhook, array &$options): array
    {
        if (!isset($options['meta_data']['init'])) {
            $options['meta_data']['init'] = [
                'magento_version' => $this->productMetaDataInterface->getVersion(),
                'data_version' => self::DATA_VERSION
            ];
        }

        $options['meta_data']['magento_version'] = $this->productMetaDataInterface->getVersion();
        return $options;
    }
}
