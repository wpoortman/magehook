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

use MageHook\Hook\Helper\Events as EventsHelper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column as UiCoreColumn;

/**
 * Class Event
 *
 * @package MageHook\Hook\Ui\Component\Listing\Column
 */
class Event extends UiCoreColumn
{
    /** @var EventsHelper */
    protected $eventsHelper;

    /**
     * Event constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param EventsHelper $eventsHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        EventsHelper $eventsHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->eventsHelper = $eventsHelper;
    }

    /**
     * Show event labels instead of the internal name.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $value) {
                $dataSource['data']['items'][$key]['event'] = $this->eventsHelper->getEventLabel($value['event']);
            }
        }

        return $dataSource;
    }
}
