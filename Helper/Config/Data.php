<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Helper\Config;

use Magento\Backend\Model\Auth\StorageInterface as AdminStorageInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Data
 *
 * @package MageHook\Hook\Helper\Config
 */
class Data
{
    public const XML_PATH_HOOK_GENERAL_ACTIVE = 'hook/general/active';
    public const XML_PATH_HOOK_GENERAL_ENVIRONMENT = 'hook/general/environment';

    /** @var ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var AdminStorageInterface $adminStorageInterface */
    protected $adminStorageInterface;

    /** @var null|array $requestParams */
    protected $requestParams;

    /**
     * Data constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param AdminStorageInterface $adminStorageInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        AdminStorageInterface $adminStorageInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->adminStorageInterface = $adminStorageInterface;
    }

    /**
     * Returns if hooks can be triggered.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->scopeConfig->isSetFlag(self::XML_PATH_HOOK_GENERAL_ACTIVE);
    }

    /**
     * Returns the current store environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_HOOK_GENERAL_ENVIRONMENT);
    }
}
