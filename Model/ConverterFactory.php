<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model;

use Magento\Framework\ObjectManagerInterface;
use RuntimeException;

/**
 * Class ConverterFactory
 *
 * @package MageHook\Hook\Model
 */
class ConverterFactory
{
    /** @var ObjectManagerInterface $objectManagerInterface */
    protected $objectManagerInterface;

    /**
     * ConverterFactory constructor.
     *
     * @param ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(
        ObjectManagerInterface $objectManagerInterface
    ) {
        $this->objectManagerInterface = $objectManagerInterface;
    }

    /**
     * @param string $converterClass
     * @return ConverterInterface
     */
    public function create(string $converterClass): ConverterInterface
    {
        if (empty($converterClass)) {
            throw new RuntimeException('Converter class can not be empty');
        }

        /** @var ConverterInterface $instance */
        $instance = $this->objectManagerInterface->create($converterClass);

        if (!$instance instanceof ConverterInterface) {
            throw new \InvalidArgumentException(
                $converterClass . ' does not implement ' . ConverterInterface::class
            );
        }

        return $instance;
    }
}
