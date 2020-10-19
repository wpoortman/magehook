<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Ui\DataProvider\Form;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Api\WebhookRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

/**
 * Class Webhook
 *
 * @package MageHook\Hook\Ui\DataProvider\Form
 */
class Webhook extends DataProvider
{
    /**
     * Form area's
     */
    public const AREA_GLOBAL    = 'global';
    public const AREA_WEBHOOK   = 'webhook';
    public const AREA_OPTIONS   = 'options';
    public const AREA_DEVELOPER = 'developer';

    /** @var WebhookRepositoryInterface $webhookRepositoryInterface */
    protected $webhookRepositoryInterface;

    /**
     * Webhook constructor.
     *
     * @param string                     $name
     * @param string                     $primaryFieldName
     * @param string                     $requestFieldName
     * @param ReportingInterface         $reporting
     * @param SearchCriteriaBuilder      $searchCriteriaBuilder
     * @param RequestInterface           $request
     * @param FilterBuilder              $filterBuilder
     * @param WebhookRepositoryInterface $webhookRepositoryInterface
     * @param array                      $meta
     * @param array                      $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        WebhookRepositoryInterface $webhookRepositoryInterface,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->webhookRepositoryInterface = $webhookRepositoryInterface;
    }
    /**
     * {@inheritDoc}
     */
    public function getData(): array
    {
        return $this->formatOutput($this->getSearchResult());
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchResult(): SearchResultsInterface
    {
        $this->searchCriteria = $this->searchCriteriaBuilder->create();
        $this->searchCriteria->setRequestName($this->name);

        return $this->webhookRepositoryInterface->getList($this->getSearchCriteria());
    }

    /**
     * {@inheritDoc}
     */
    private function formatOutput(SearchResultsInterface $searchResult): array
    {
        $properties = [
            self::AREA_WEBHOOK => [
                WebhookInterface::URL,
                WebhookInterface::NAME,
                WebhookInterface::IS_ACTIVE,
                WebhookInterface::EVENT
            ],
            self::AREA_DEVELOPER => [
                WebhookInterface::DEPLOYMENT_MODE,
                WebhookInterface::ONLY_SIGNAL,
                WebhookInterface::QUERY_DATA,
            ],
            self::AREA_OPTIONS => [
                WebhookInterface::CUSTOM_OPTIONS
            ]
        ];

        $items = [];

        /** @var WebhookInterface $item */
        foreach ($searchResult->getItems() as $item) {
            $data = [
                WebhookInterface::ID => $item->getId()
            ];

            foreach ($item->getData() as $key => $value) {
                $section = self::AREA_GLOBAL;

                if (\in_array($key, $properties[self::AREA_WEBHOOK], false)) {
                    $section = self::AREA_WEBHOOK;
                } elseif (\in_array($key, $properties[self::AREA_DEVELOPER], false)) {
                    $section = self::AREA_DEVELOPER;
                } elseif (\in_array($key, $properties[self::AREA_OPTIONS], false)) {
                    $section = self::AREA_OPTIONS;
                }

                if ($key === WebhookInterface::QUERY_DATA) {
                    $data[$section][$key] = $this->getQueryData($item);
                } elseif ($key === WebhookInterface::CUSTOM_OPTIONS) {
                    $data[$section] = $item->getCustomOptions();
                } else {
                    $data[$section][$key] = $value;
                }
            }

            $items[$item->getId()] = $data;
        }

        return $items;
    }

    /**
     * Transform 'query_data' into UI-component required data
     *
     * @param WebhookInterface $item
     *
     * @return array
     */
    public function getQueryData(WebhookInterface $item): array
    {
        $result = $item->getQueryData();

        if (!\is_array($result) || \count($result) === 0) {
            $result = [
                WebhookInterface::QUERY_DATA => []
            ];
        }

        return $result;
    }
}
