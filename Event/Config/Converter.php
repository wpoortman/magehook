<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Event\Config;

use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Model\Queue\ConsumerTypeList;
use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 *
 * @package MageHook\Hook\Event\Config
 */
class Converter implements ConverterInterface
{
    public const XML_DEFAULT_VALUE_LIST    = 'adminhtml';
    public const XML_DEFAULT_VALUE_REQUEST = 'async';
    public const XML_DEFAULT_VALUE_TYPE    = ConsumerTypeList::DEFAULT_TYPE;

    /**
     * Optional webhook.xml attributes.
     *
     * @var array
     */
    public const OPTIONAL_ATTRIBUTES = [
        'purpose',
        'service',
        'converter',
        'validator',
        'type',
        'request',
        'list'
    ];

    /**
     * Custom convert method to add support for extra node attributes on <event> tag level.
     *
     * @param \DOMDocument $source
     * @return array [$eventName => [..$config]]
     */
    public function convert($source): array
    {
        $output = [];

        /** @var \DOMNodeList $events */
        $hooks = $source->getElementsByTagName('hook');

        /** @var \DOMNode $eventConfig */
        foreach ($hooks as $eventConfig) {
            $config = [];

            // Is required so has XSD validation
            $name  = $eventConfig->attributes->getNamedItem(EventsHelper::EVENT_NAME)->nodeValue;
            // Is required so has XSD validation
            $title = $eventConfig->attributes->getNamedItem(EventsHelper::EVENT_ATTRIBUTE_TITLE)->nodeValue;
            // Is required so has XSD validation
            $group = $eventConfig->attributes->getNamedItem(EventsHelper::EVENT_ATTRIBUTE_GROUP)->nodeValue;

            foreach (self::OPTIONAL_ATTRIBUTES as $attribute) {
                $config[$attribute] = $eventConfig->attributes->getNamedItem($attribute)->nodeValue ?? null;
            }

            if ($config[EventsHelper::EVENT_ATTRIBUTE_TYPE] === null) {
                $config[EventsHelper::EVENT_ATTRIBUTE_TYPE] = self::XML_DEFAULT_VALUE_TYPE;
            }
            if ($config[EventsHelper::EVENT_ATTRIBUTE_REQUEST] === null) {
                $config[EventsHelper::EVENT_ATTRIBUTE_REQUEST] = self::XML_DEFAULT_VALUE_REQUEST;
            }
            if ($config[EventsHelper::EVENT_ATTRIBUTE_LIST] === null) {
                $config[EventsHelper::EVENT_ATTRIBUTE_LIST] = self::XML_DEFAULT_VALUE_LIST;
            }

            $config[EventsHelper::EVENT_NAME]            = $name;
            $config[EventsHelper::EVENT_ATTRIBUTE_TITLE] = __($title)->getText();
            $config[EventsHelper::EVENT_ATTRIBUTE_GROUP] = __($group)->getText();

            // Not required fields
            $config[EventsHelper::EVENT_ELEMENT_OBSERVER] = $name;

            $output[$name] = $config;
        }

        return $output;
    }
}
