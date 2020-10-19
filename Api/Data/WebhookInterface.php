<?php
/**
 * @author  MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface WebhookInterface
 *
 * @package MageHook\Hook\Api\Data
 *
 * @api
 */
interface WebhookInterface extends ExtensibleDataInterface
{
    /**
     * Table Columns.
     */
    public const ID = 'id';
    public const CREATED_AT = 'created_at';
    public const DEPLOYMENT_MODE = 'deployment_mode';
    public const TYPE = 'type';
    public const IS_ACTIVE = 'is_active';
    public const NAME = 'name';
    public const EVENT = 'event';
    public const URL = 'url';
    public const COMMENT = 'comment';
    public const QUERY_DATA = 'query_data';
    public const ONLY_SIGNAL = 'only_signal';
    public const CUSTOM_OPTIONS = 'custom_options';

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_CONCEPT = 2;

    /**
     * @return null|string|int
     */
    public function getId();

    /**
     * @return int
     */
    public function getCreatedAt(): int;

    /**
     * @return string
     */
    public function getDeploymentMode(): string;

    /**
     * @param string $string
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setDeploymentMode($string): self;

    /**
     * @return int
     */
    public function getIsActive(): int;

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setIsActive(int $status): self;

    /**
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setIsNotActive(): self;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $string
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setName($string): self;

    /**
     * @return string
     */
    public function getEvent(): string;

    /**
     * @param      $string
     * @param bool $validate
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setEvent($string, $validate = false): self;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param $string
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setUrl($string): self;

    /**
     * @return string[]
     */
    public function getQueryData(): array;

    /**
     * @param $data
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setQueryData($data): self;

    /**
     * @return string[]
     */
    public function getCustomOptions(): array;

    /**
     * @param array $options
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setCustomOptions(array $options): self;

    /**
     * @return int
     */
    public function getOnlySignal(): int;

    /**
     * @param $int
     *
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function setOnlySignal($int): self;
}
