<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook;

/**
 * Interface ManagerInterface
 *
 * @package MageHook\Hook
 */
interface ManagerInterface
{
    public const WRAPPER_DK_EVENT    = 'event';
    public const WRAPPER_DK_WEBHOOK  = 'webhook';
    public const WRAPPER_DK_BODY     = 'body';
    public const WRAPPER_DK_RESOURCE = 'resource';
    public const WRAPPER_DK_METADATA = 'meta_data';

    /**
     * Calls all observer callbacks registered for this webhook
     * and multiple observers matching webhook name pattern.
     *
     * @param string $event Event name
     * @param array|object $data Body data
     * @param array $metadata Additional meta data
     * @return void
     */
    public function dispatch($event, $data, $metadata = []): void;

    /**
     * @alias dispatch
     *
     * @param       $event
     * @param       $data
     * @param array $metadata
     */
    public function fire($event, $data, $metadata = []): void;
}
