<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Model;

/**
 * A converter catches all data which is passing
 * through the event dispatchment
 *
 * Interface ConverterInterface
 *
 * @package MageHook\Hook\Api\Data
 *
 * @api
 */
interface ConverterInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function convert(array $data): array;
}
