<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Helper;

use MageHook\Hook\Event\ConfigInterface;

/**
 * Class Events
 *
 * @package MageHook\Hook\Helper
 */
class Events
{
    /** @var string Element event name */
    public const EVENT_NAME = 'event';

    /** @var string Event element observer name */
    public const EVENT_ELEMENT_OBSERVER = 'observer';

    /** @var string Event title */
    public const EVENT_ATTRIBUTE_TITLE = 'title';

    /** @var string Event group */
    public const EVENT_ATTRIBUTE_GROUP = 'group';

    /** @var string Event purpose */
    public const EVENT_ATTRIBUTE_PURPOSE = 'purpose';

    /** @var string Converter service class */
    public const EVENT_ATTRIBUTE_SERVICE_CLASS = 'service';

    /** @var string Converter class */
    public const EVENT_ATTRIBUTE_CONVERTER_CLASS = 'converter';

    /** @var string Validator class */
    public const EVENT_ATTRIBUTE_VALIDATOR_CLASS = 'validator';

    /** @var string Event disabled */
    public const EVENT_ATTRIBUTE_DISABLED = 'disabled';

    /** @var string Event type */
    public const EVENT_ATTRIBUTE_TYPE = 'type';

    /** @var string Event request */
    public const EVENT_ATTRIBUTE_REQUEST = 'request';

    /** @var string Event list */
    public const EVENT_ATTRIBUTE_LIST = 'list';

    /** @var null|array All webhook events */
    protected $events;

    /** @var null|array All webhook events sorted by list */
    protected $list;

    /** @var ConfigInterface $config */
    protected $config;

    /**
     * Events constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Get all webhook events.
     *
     * @param string $list
     * @return array
     */
    public function getList(string $list = 'adminhtml'): ?array
    {
        if (!\is_array($this->events)) {
            $this->events = $this->config->getHooks(null);

            foreach ($this->events as $event) {
                if ($event[self::EVENT_ATTRIBUTE_LIST] === $list) {
                    $this->list[$event[self::EVENT_NAME]] = $event;
                }
            }
        }

        if ($list === null) {
            return $this->events;
        }

        return $this->list;
    }

    /**
     * Get event by event name.
     *
     * @param $event
     * @return array|bool
     */
    public function getByEvent($event)
    {
        $list = $this->getList();

        if (\count($list) === 0 || !isset($list[$event])) {
            return false;
        }

        return $list[$event];
    }

    /**
     * Get event label.
     *
     * @todo Should be replaced to \MageHook\Hook\Event\Config\Converter in order to have it pre-cached
     *
     * @param string $eventName
     * @return string
     */
    public function getEventLabel($eventName): string
    {
        $info = $this->getByEvent($eventName);

        if (empty($info)) {
            // Add at least a title if the event doesnt exist any longer
            $label = $info[self::EVENT_ATTRIBUTE_TITLE] = 'Unknown Event Label';
        } else {
            $label = \implode(' - ', [
                \ucfirst($info[self::EVENT_ATTRIBUTE_GROUP]),
                \ucfirst($info[self::EVENT_ATTRIBUTE_TITLE])
            ]);

            if ($info[self::EVENT_ATTRIBUTE_PURPOSE]) {
                $label .= ' (' . $info[self::EVENT_ATTRIBUTE_PURPOSE] . ')';
            }
        }

        return \ucfirst(\strtolower($label));
    }

    /**
     * Check if event exists.
     *
     * @param string $event
     * @return bool
     */
    public function exists($event): bool
    {
        return \is_array($this->getByEvent($event));
    }
}
