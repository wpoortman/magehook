<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Queue;

use MageHook\Hook\Model\Queue\Consumer\TypeInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ConsumerInterface
 *
 * @package MageHook\Hook\Api\Data
 *
 * @api
 */
class ConsumerTypeList
{
    public const DEFAULT_TYPE = 'default';

    /** @var TypeInterface[] $types */
    protected $types;

    /**
     * ConsumerTypeList constructor.
     *
     * @param TypeInterface[] $types
     */
    public function __construct(
        array $types = []
    ) {
        $this->types = $types;
    }

    /**
     * Get all consumer types.
     *
     * @return TypeInterface[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param string $type
     *
     * @return TypeInterface
     * @throws NoSuchEntityException
     */
    public function getType(string $type): TypeInterface
    {
        $types = $this->getTypes();

        if (!isset($types[$type])) {
            $type = $this->getDefaultType();
        }

        return $types[$type];
    }

    /**
     * @return TypeInterface
     * @throws NoSuchEntityException
     */
    public function getDefaultType(): TypeInterface
    {
        $types = $this->getTypes();

        if (!isset($types[self::DEFAULT_TYPE])) {
            throw new NoSuchEntityException(
                __('Consumer type %1 does not exist', [self::DEFAULT_TYPE])
            );
        }

        return $types[self::DEFAULT_TYPE];
    }
}
