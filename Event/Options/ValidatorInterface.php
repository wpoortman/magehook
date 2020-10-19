<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Event\Options;

use Magento\Framework\DataObject;

/**
 * Interface ValidatorInterface
 *
 * @package MageHook\Hook\Event\Options
 *
 * @api
 */
interface ValidatorInterface
{
    /**
     * @return DataObject
     */
    public function getData(): DataObject;

    /**
     * @return DataObject
     */
    public function getOptions(): DataObject;

    /**
     * @return array
     */
    public function getEvent(): array;

    /**
     * @return mixed
     */
    public function getResource();

    /**
     * @return array
     */
    public function getBody(): array;
}
