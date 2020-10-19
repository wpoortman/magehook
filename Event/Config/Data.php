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

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data\Scoped;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Data
 *
 * Provides event configuration
 *
 * @package MageHook\Hook\Event\Config
 */
class Data extends Scoped
{
    /**
     * Scope priority loading scheme.
     *
     * @var array $_scopePriorityScheme
     */
    protected $_scopePriorityScheme = ['global'];

    /**
     * Data constructor.
     *
     * @param Reader $reader
     * @param ScopeInterface $configScope
     * @param CacheInterface $cache
     * @param string $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        Reader $reader,
        ScopeInterface $configScope,
        CacheInterface $cache,
        $cacheId = 'webhook_config_cache',
        SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId, $serializer);
    }
}
