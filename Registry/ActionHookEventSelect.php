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

use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class ActionHookEventSelect
 *
 * @package MageHook\Hook\Registry
 */
class ActionHookEventSelect
{
    // Refers to view/adminhtml/ui_component/{NAMESPACE_PREFIX}{DEFAULT_IDENTIFIER}
    public const DEFAULT_IDENTIFIER = 'none';
    public const NAMESPACE_PREFIX   = 'webhook_options_';

    /** @var null|string $identifier */
    private $identifier;

    /**
     * @param string $identifier
     * @param bool   $validate
     *
     * @return $this
     * @throws AlreadyExistsException
     */
    public function set(string $identifier, $validate = true): self
    {
        if ($validate && $identifier === self::DEFAULT_IDENTIFIER) {
            throw new AlreadyExistsException(
                __('Namespace can not be the same as default')
            );
        }

        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Get selected event namespace.
     *
     * @param bool $includePrefix
     *
     * @return string
     */
    public function get($includePrefix = true): string
    {
        return $this->generate($this->identifier ?? self::DEFAULT_IDENTIFIER, $includePrefix);
    }

    /**
     * @return bool
     */
    public function has(): bool
    {
        return $this->identifier !== null;
    }

    /**
     * @param bool $includePrefix
     *
     * @return string
     */
    public function getDefault($includePrefix = false): string
    {
        return $this->generate(self::DEFAULT_IDENTIFIER, $includePrefix);
    }

    /**
     * @param string $identifier
     * @param bool   $includePrefix
     *
     * @return string
     */
    public function generate(string $identifier, bool $includePrefix = false): string
    {
        if ($includePrefix) {
            $identifier = self::NAMESPACE_PREFIX . $identifier;
        }

        return strtolower($identifier);
    }
}
