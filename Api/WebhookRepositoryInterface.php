<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace MageHook\Hook\Api;

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface WebhookRepositoryInterface
 *
 * @package MageHook\Hook\Api
 *
 * @api
 */
interface WebhookRepositoryInterface
{
    public const TYPE_ADMINHTML = 'adminhtml';

    /**
     * Get a webhook.
     *
     * @param int|string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \MageHook\Hook\Api\Data\WebhookInterface
     */
    public function get($id): WebhookInterface;

    /**
     * Save webhook.
     *
     * @param \MageHook\Hook\Api\Data\WebhookInterface $statistics
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \MageHook\Hook\Api\Data\WebhookInterface|\MageHook\Hook\Model\ResourceModel\Webhook
     */
    public function save(WebhookInterface $statistics);

    /**
     * Get all available webhooks.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface;

    /**
     * Get all available webhooks by type.
     *
     * @param $name
     * @param string $type
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByName($name, $type = self::TYPE_ADMINHTML): SearchResultsInterface;

    /**
     * Delete a webhook.
     *
     * @param \MageHook\Hook\Api\Data\WebhookInterface $webhook
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return mixed
     */
    public function delete(WebhookInterface $webhook);
}
