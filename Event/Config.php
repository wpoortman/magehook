<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Event;

use MageHook\Hook\Event\Config\Data;

/**
 * Class Config
 *
 * @package MageHook\Hook\Event
 */
class Config implements ConfigInterface
{
    /**
     * Modules configuration model.
     *
     * @var Data $dataContainer
     */
    protected $dataContainer;

    /**
     * Config constructor.
     *
     * @param Data $dataContainer
     */
    public function __construct(Data $dataContainer)
    {
        $this->dataContainer = $dataContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getHooks($eventName): array
    {
        return $this->dataContainer->get($eventName, []);
    }
}
