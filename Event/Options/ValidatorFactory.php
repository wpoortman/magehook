<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Event\Options;

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Framework\Event\Observer as ObserverEvent;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ValidatorFactory
 *
 * @package MageHook\Hook\Event\Options
 */
class ValidatorFactory
{
    /** @var ObjectManagerInterface $objectManagerInterface */
    protected $objectManagerInterface;

    /** @var array $instances */
    protected $instances = [];

    /**
     * ValidatorFactory constructor.
     *
     * @param ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(
        ObjectManagerInterface $objectManagerInterface
    ) {
        $this->objectManagerInterface = $objectManagerInterface;
    }

    /**
     * @param WebhookInterface $webhookInterface
     * @param ObserverEvent    $observerEvent
     * @param string           $validatorClass
     *
     * @return ValidatorInterface
     */
    public function create(
        WebhookInterface $webhookInterface,
        ObserverEvent $observerEvent,
        string $validatorClass
    ): ValidatorInterface {
        if (!isset($this->instances[$validatorClass])) {
            /** @var ValidatorInterface $instance */
            $instance = $this->objectManagerInterface->create($validatorClass, [
                'webhookInterface' => $webhookInterface,
                'observerEvent' => $observerEvent
            ]);

            if (!$instance instanceof ValidatorInterface) {
                throw new \InvalidArgumentException(
                    $validatorClass . ' does not implement ' . ValidatorInterface::class
                );
            }

            $this->instances[$validatorClass] = $instance;
        }

        return $this->instances[$validatorClass];
    }
}
