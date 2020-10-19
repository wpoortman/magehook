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

use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Registry\ActionHookEventSelect as ActionHookEventSelectRegistry;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Event
 *
 * @package MageHook\Hook\Model\Config\Source\Webhook
 */
class Event implements OptionSourceInterface
{
    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /** @var ActionHookEventSelectRegistry $actionHookEventSelectRegistry */
    protected $actionHookEventSelectRegistry;

    /**
     * Event constructor.
     *
     * @param EventsHelper                  $eventsHelper
     * @param ActionHookEventSelectRegistry $actionHookEventSelectRegistry
     */
    public function __construct(
        EventsHelper $eventsHelper,
        ActionHookEventSelectRegistry $actionHookEventSelectRegistry
    ) {
        $this->eventsHelper = $eventsHelper;
        $this->actionHookEventSelectRegistry = $actionHookEventSelectRegistry;
    }

    /**
     * List of events based on all webhooks.xml file.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        /** @var array|null $items */
        $items = $this->eventsHelper->getList();

        if (\is_array($items)) {
            foreach ($items as $event) {
                $options[] = [
                    'value' => $event[EventsHelper::EVENT_NAME],
                    'label' => $this->getEventLabel($event)
                ];
            }
        }

        return $options;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray(): array
    {
        $options = [];

        // Will only show the 'adminhtml' list events
        foreach ($this->eventsHelper->getList() as $event) {
            $options[] = [
                $event[EventsHelper::EVENT_NAME] => $this->getEventLabel($event),
            ];
        }

        return $options;
    }

    /**
     * Get the label for the event <option>.
     *
     * @param $info
     * @return string
     */
    protected function getEventLabel($info): string
    {
        $label = __('Unknown Event Title');

        if (!empty($info)) {
            $label = implode(' - ', [
                ucfirst($info[EventsHelper::EVENT_ATTRIBUTE_GROUP]),
                ucfirst($info[EventsHelper::EVENT_ATTRIBUTE_TITLE])
            ]);

            if (isset($info[EventsHelper::EVENT_ATTRIBUTE_PURPOSE])) {
                $label .= ' (' . $info[EventsHelper::EVENT_ATTRIBUTE_PURPOSE] . ')';
            }
        }

        return $label;
    }
}
