<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Api\WebhookRepositoryInterface;
use MageHook\Hook\Model\Config\Source\Webhook\DeploymentMode;
use MageHook\Hook\Model\ResourceModel\Webhook as WebhookResourceModel;
use MageHook\Hook\Model\ResourceModel\Webhook\CollectionFactory as WebhookCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\State as ApplicationState;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class WebhookRepository
 *
 * @package MageHook\Hook\Model
 */
class WebhookRepository implements WebhookRepositoryInterface
{
    /** @var ApplicationState $applicationState */
    protected $applicationState;

    /** @var WebhookResourceModel $transferStatisticsResource */
    protected $webhookResourceModel;

    /** @var WebhookFactory $webhookFactory */
    protected $webhookFactory;

    /** @var WebhookCollectionFactory $webhookCollectionFactory */
    protected $webhookCollectionFactory;

    /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var SearchResultsInterfaceFactory $searchResultsInterfaceFactory */
    protected $searchResultsInterfaceFactory;

    /** @var CollectionProcessorInterface $collectionProcessor */
    protected $collectionProcessor;

    /**
     * WebhookRepository constructor.
     *
     * @param ApplicationState $applicationState
     * @param WebhookResourceModel $webhookResourceModel
     * @param WebhookFactory $webhookFactory
     * @param WebhookCollectionFactory $webhookCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ApplicationState $applicationState,
        WebhookResourceModel $webhookResourceModel,
        WebhookFactory $webhookFactory,
        WebhookCollectionFactory $webhookCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchResultsInterfaceFactory $searchResultsInterfaceFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->applicationState = $applicationState;
        $this->webhookResourceModel = $webhookResourceModel;
        $this->webhookFactory = $webhookFactory;
        $this->webhookCollectionFactory = $webhookCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsInterfaceFactory = $searchResultsInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id): WebhookInterface
    {
        /** @var WebhookInterface $webhook */
        $webhook = $this->webhookFactory->create();

        try {
            $this->webhookResourceModel->load($webhook, (int)$id);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Webhook with id "%1" does not exist', $id));
        }

        return $webhook;
    }

    /**
     * {@inheritDoc}
     */
    public function save(WebhookInterface $webhook)
    {
        try {
            $webhook->setQueryData($webhook->getQueryData());
            return $this->webhookResourceModel->save($webhook);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the webhook'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        $collection = $this->webhookCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsInterfaceFactory->create();

        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritDoc}
     */
    public function getListByName($name, $type = self::TYPE_ADMINHTML): SearchResultsInterface
    {
        return $this->getList($this->searchCriteriaBuilder
            ->addFilter(WebhookInterface::TYPE, $type)
            ->addFilter(WebhookInterface::EVENT, $name)
            ->addFilter(WebhookInterface::IS_ACTIVE, WebhookInterface::STATUS_ACTIVE)
            ->addFilter(WebhookInterface::DEPLOYMENT_MODE, [
                DeploymentMode::MODE_INDEPENDENT,
                $this->applicationState->getMode()
            ], 'in')
            ->create());
    }

    /**
     * {@inheritDoc}
     */
    public function delete(WebhookInterface $webhook)
    {
        try {
            $this->webhookResourceModel->delete($webhook);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete webhook'));
        }
    }
}
