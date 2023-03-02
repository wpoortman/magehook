<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Model\Queue\Consumer;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Model\Queue\OperationMessageInterface;

/**
 * Interface ConsumerInterface
 *
 * @package MageHook\Hook\Api\Data
 *
 * @api
 */
interface TypeInterface
{
    public const CONFIG_HANDLER = 'handler';
    public const CONFIG_MIDDLEWARE = 'middleware';

    /**
     * System config group (section/group/{group}/).
     */
    public const CONFIG_GROUP = 'type_default';

    /**
     * Request Method.
     */
    public const METHOD = 'POST';

    /**
     * Create and send an HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string $method  HTTP method.
     * @param string $uri     URI object or string.
     * @param array  $options
     *
     * @return mixed
     */
    public function request(string $method, $uri = '', array $options = []);

    /**
     * @return OperationMessageInterface
     */
    public function getOperation(): OperationMessageInterface;

    /**
     * @param OperationMessageInterface $operation
     * @return TypeInterface
     */
    public function setOperation(OperationMessageInterface $operation): TypeInterface;

    /**
     * @return WebhookInterface
     */
    public function getHook(): WebhookInterface;

    /**
     * @param WebhookInterface $hook
     * @return TypeInterface
     */
    public function setHook(WebhookInterface $hook): TypeInterface;

    /**
     * Returns the request method.
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Retrieve system config value by key.
     *
     * The configuration tree path is partly predefined
     * based on the self::CONFIG_GROUP Type constant.
     *
     * @param null $key
     * @param null $default
     * @param string $type
     * @return mixed
     */
    public function getSystemConfig($key = null, $default = null, $type = self::CONFIG_GROUP);
}
