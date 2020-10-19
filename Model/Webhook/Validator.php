<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Webhook;

use MageHook\Hook\Api\Data\WebhookInterface;
use Zend_Validate_Interface;

/**
 * Class Validate
 *
 * Handles the complete validation of data where it's saved via a Action or Interface
 *
 * @package MageHook\Hook\Model\Webhook
 */
class Validator implements Zend_Validate_Interface
{
    /** @var array $messages */
    protected $messages = [];

    /**
     * @param WebhookInterface $value
     * @return bool
     */
    public function isValid($value): bool
    {
        return $this->forDoublePrefixEvents($value);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return \count($this->messages) === 0;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function newMessage(string $message): self
    {
        $this->messages[] = __($message);
        return $this;
    }

    /**
     * @param WebhookInterface $value
     * @return bool
     */
    public function forDoublePrefixEvents(WebhookInterface $value): bool
    {
        $value = $value->getData();

        if (!isset($value[WebhookInterface::DEPLOYMENT_MODE])) {
            $this->newMessage('No deployment mode set');
        }

        return $this->validate();
    }
}
