<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Helper\Config\Hook;

use MageHook\Hook\Model\Queue\Consumer\TypeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ConsumerType
 *
 * @package MageHook\Hook\Helper\Config\Hook
 */
class ConsumerType
{
    public const XML_PATH_SECTION = 'hook';
    public const XML_PATH_GROUP   = 'consumer_type';

    /** @var ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;

    /**
     * ConsumerType constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get consumer type value.
     *
     * @param string $type
     * @param string $value
     * @return mixed
     */
    public function getValue(string $type, string $value)
    {
        $value = $this->scopeConfig->getValue($this->getPath($type, $value));

        if (is_string($value) && $type !== TypeInterface::CONFIG_GROUP) {
            $value = $this->scopeConfig->getValue($this->getPath(TypeInterface::CONFIG_GROUP, $value));
        }

        return $value;
    }

    /**
     * Get consumer type system configuration path.
     *
     * @param string $type
     * @param string $value
     * @return string
     */
    private function getPath(string $type, string $value): string
    {
        return implode('/', [self::XML_PATH_SECTION, self::XML_PATH_GROUP, $type, $value]);
    }
}
