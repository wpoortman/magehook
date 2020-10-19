<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Queue\Consumer\Type;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Helper\Config\Hook\ConsumerType as ConsumerTypeHelper;
use MageHook\Hook\Model\Queue\Consumer\TypeInterface;
use MageHook\Hook\Model\Queue\OperationMessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractType
 *
 * @package MageHook\Hook\Model\Queue\Consumer\Type
 *
 * @api
 */
abstract class AbstractType extends Client implements TypeInterface
{
    /** @var OperationMessageInterface $operation */
    protected $operation;

    /** @var WebhookInterface $hook */
    protected $hook;

    /** @var ConsumerTypeHelper $consumerTypeHelper */
    protected $consumerTypeHelper;

    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /**
     * AbstractType constructor.
     *
     * Try to inject this object as a Proxy if possible
     *
     * @param ConsumerTypeHelper $consumerTypeHelper
     * @param LoggerInterface    $loggerInterface
     * @param array              $config
     */
    public function __construct(
        ConsumerTypeHelper $consumerTypeHelper,
        LoggerInterface $loggerInterface,
        array $config = []
    ) {
        $this->consumerTypeHelper = $consumerTypeHelper;
        $this->loggerInterface = $loggerInterface;

        $stack = HandlerStack::create(
            $config[self::CONFIG_HANDLER] ?? null
        );

        $this->prepareMiddleware($stack, $config);

        $config[self::CONFIG_HANDLER] = $stack;
        parent::__construct($config);
    }

    /**
     * Wrapper method.
     *
     * {@inheritDoc}
     */
    public function request($method, $uri = '', array $options = [])
    {
        try {
            return parent::request($method, $uri, $options);
        } catch (GuzzleException $exception) {
            $this->loggerInterface->critical($exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOperation(): OperationMessageInterface
    {
        return $this->operation;
    }

    /**
     * {@inheritDoc}
     */
    public function setOperation(OperationMessageInterface $operation): TypeInterface
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHook(): WebhookInterface
    {
        return $this->hook;
    }

    /**
     * {@inheritDoc}
     */
    public function setHook(WebhookInterface $hook): TypeInterface
    {
        $this->hook = $hook;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod(): string
    {
        return self::METHOD;
    }

    /**
     * {@inheritDoc}
     */
    public function getSystemConfig($key = null, $default = null, $type = self::CONFIG_GROUP)
    {
        $value = null;

        if (\is_string($key)) {
            $value = $this->consumerTypeHelper->getValue($type, $key);
        }

        return $value ?? $default;
    }

    /**
     * Prepare default middleware
     *
     * Before/After: Use a Plugin on the Abstract class in order to be able
     *               to push a middleware before or after the default
     *
     * @link https://devdocs.magento.com/guides/v2.3/extension-dev-guide/plugins.html
     *
     * @param callable $stack
     * @param array $config
     * @return callable
     *
     * @api
     */
    public function prepareMiddleware(callable $stack, array $config): callable
    {
        if (isset($config[self::CONFIG_MIDDLEWARE])) {
            foreach ($config[self::CONFIG_MIDDLEWARE] as $name => $callable) {
                $stack->push($callable, $name);
            }
        }

        return $stack;
    }
}
