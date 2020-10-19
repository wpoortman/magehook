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
 * Class Status
 *
 * @package MageHook\Hook\Ui\Component\Listing\Column
 */
class Status extends UiCoreColumn
{
    /**
     * Show event labels instead of the internal name
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $value) {
                switch ($dataSource['data']['items'][$key]['is_active']) {
                    case WebhookInterface::STATUS_INACTIVE:
                        $dataSource['data']['items'][$key]['status'] = __('Disabled');
                        break;
                    case WebhookInterface::STATUS_ACTIVE:
                        $dataSource['data']['items'][$key]['status'] = __('Enabled');
                        break;
                    case WebhookInterface::STATUS_CONCEPT:
                        $dataSource['data']['items'][$key]['status'] = __('Concept');
                        break;
                    default:
                        $dataSource['data']['items'][$key]['status'] = __('Unknown');
                }
            }
        }

        return $dataSource;
    }
}
