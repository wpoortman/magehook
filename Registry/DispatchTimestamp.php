<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Registry;

use Psr\Log\LoggerInterface;

/**
 * Class ManagerDispatchment
 *
 * @package MageHook\Hook\Service
 */
class DispatchTimestamp
{
    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /** @var int $timestamp */
    private $timestamp = 0;

    /**
     * ManagerDispatchment constructor.
     *
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        LoggerInterface $loggerInterface
    ) {
        $this->loggerInterface = $loggerInterface;
    }

    /**
     * Set timestamp.
     *
     * @return $this
     */
    public function set(): self
    {
        if ($this->get() === 0) {
            try {
                $date = new \DateTime();
                $this->timestamp = $date->getTimestamp();
            } catch (\Exception $exception) {
                $this->loggerInterface->error($exception->getMessage());
            }
        }

        return $this;
    }

    /**
     * Get timestamp.
     *
     * @return int
     */
    public function get(): int
    {
        return $this->timestamp;
    }
}
