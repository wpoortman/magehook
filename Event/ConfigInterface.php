<?php
/**
 * Event configuration model interface
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */
namespace MageHook\Hook\Event;

/**
 * Interface ConfigInterface
 *
 * @package MageHook\Hook\Event
 */
interface ConfigInterface
{
    /**
     * Event types.
     */
    public const TYPE_CORE   = 'core';
    public const TYPE_CUSTOM = 'custom';

    /**
     * Get hooks by event name.
     *
     * @param string $eventName
     * @return array
     */
    public function getHooks($eventName): array;
}
