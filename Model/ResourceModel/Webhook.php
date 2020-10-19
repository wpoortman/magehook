<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\ResourceModel;

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Webhook
 *
 * @package MageHook\Hook\Model\ResourceModel
 */
class Webhook extends AbstractDb
{
    public const TABLE_NAME = 'magehook_webhook';

    /**
     * Webhook resource model Constructor.
     */
    public function _construct(): void
    {
        $this->_init(self::TABLE_NAME, WebhookInterface::ID);
    }
}
