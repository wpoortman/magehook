<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\ResourceModel\Webhook;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Model\ResourceModel\Webhook as WebhookResourceModel;
use MageHook\Hook\Model\Webhook as WebhookModel;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package MageHook\Hook\Model\ResourceModel\Webhook
 */
class Collection extends AbstractCollection
{
    /** @var string $_idFieldName */
    protected $_idFieldName = WebhookInterface::ID;

    /** @var null|array $eventMap */
    protected $eventMap;

    /** @var DataObject[] $_events */
    protected $_events = [];

    /**
     * Init (resource) model.
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(
            WebhookModel::class,
            WebhookResourceModel::class
        );
    }
}
