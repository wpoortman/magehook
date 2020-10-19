<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Ui\Component\Listing\Column;

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Ui\Component\Listing\Columns\Column as UiCoreColumn;

/**
 * Class DeploymentMode
 *
 * @package MageHook\Hook\Ui\Component\Listing\Column
 */
class DeploymentMode extends UiCoreColumn
{
    /**
     * Show event labels instead of the internal name
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $value) {
                $dataSource['data']['items'][$key][WebhookInterface::DEPLOYMENT_MODE]
                    = \ucfirst($dataSource['data']['items'][$key][WebhookInterface::DEPLOYMENT_MODE]);
            }
        }

        return $dataSource;
    }
}
