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
use MageHook\Hook\Helper\Events as EventsHelper;
use MageHook\Hook\Helper\Model\Webhook as Helper;
use MageHook\Hook\Model\Config\Source\Environment;
use MageHook\Hook\Model\Config\Source\Webhook\Query\Type as QueryType;
use MageHook\Hook\Model\ResourceModel\Webhook as WebhookResourceModel;
use MageHook\Hook\Model\Webhook\Validator as DataValidator;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Url\Validator as UrlValidator;
use Zend_Validate_Interface;

/**
 * Class Webhook
 *
 * @package MageHook\Hook\Model
 */
class Webhook extends AbstractModel implements WebhookInterface
{
    public const HEADER_KEY_NAME  = 'name';
    public const HEADER_KEY_VALUE = 'value';

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var null|array $queryData */
    protected $queryData;

    /** @var null|array $customOptions */
    protected $customOptions;

    /** @var null|array $queryDataSort */
    protected $queryDataSort;

    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /** @var UrlValidator $urlValidator */
    protected $urlValidator;

    /** @var DataValidator $dataValidator*/
    protected $dataValidator;

    /** @var Helper $helper */
    protected $helper;

    /**
     * Webhook constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param SerializerInterface   $serializer
     * @param EventsHelper          $eventsHelper
     * @param UrlValidator          $urlValidator
     * @param DataValidator         $dataValidator
     * @param Helper                $helper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        EventsHelper $eventsHelper,
        UrlValidator $urlValidator,
        DataValidator $dataValidator,
        Helper $helper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->eventsHelper = $eventsHelper;
        $this->urlValidator = $urlValidator;
        $this->dataValidator = $dataValidator;
        $this->helper = $helper;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Webhook model Constructor.
     */
    public function _construct(): void
    {
        $this->_init(WebhookResourceModel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt(): int
    {
        return (int)$this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeploymentMode(): string
    {
        return $this->getData(self::DEPLOYMENT_MODE) ?? Environment::ENVIRONMENT_UNKNOWN;
    }

    /**
     * {@inheritDoc}
     */
    public function setDeploymentMode($string): WebhookInterface
    {
        return $this->setData(self::DEPLOYMENT_MODE, $string);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsActive(): int
    {
        $active = $this->getData(self::IS_ACTIVE) ?? false;
        return  $active ? (int)$active : 0;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsActive(int $status): WebhookInterface
    {
        return $this->setData(self::IS_ACTIVE, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsNotActive(): WebhookInterface
    {
        return $this->setData(self::IS_ACTIVE, self::STATUS_INACTIVE);
    }

    /**
     * @return WebhookInterface
     */
    public function setIsConcept(): WebhookInterface
    {
        return $this->setData(self::IS_ACTIVE, self::STATUS_CONCEPT);
    }

    /**
     * @return bool
     */
    public function isConcept(): bool
    {
        return (int)$this->getData(self::IS_ACTIVE) === self::STATUS_CONCEPT;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(): bool
    {
        return (bool)$this->getIsActive();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->getData(self::NAME) ?? 'Unknown';
    }

    /**
     * {@inheritDoc}
     */
    public function setName($string): WebhookInterface
    {
        return $this->setData(self::NAME, $string);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvent(): string
    {
        return $this->getData(self::EVENT) ?? '';
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    public function setEvent($string, $validate = false): WebhookInterface
    {
        if ($validate && $this->eventsHelper->exists($string)) {
            throw new LocalizedException(
                __('Specified event name does not exist')
            );
        }

        return $this->setData(self::EVENT, $string);
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(): string
    {
        return $this->getData(self::URL) ?? '';
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    public function setUrl($string, $validate = true): WebhookInterface
    {
        if ($validate && !$this->urlValidator->isValid($string)) {
            throw new LocalizedException(__('Specified URL seems to be invalid'));
        }

        return $this->setData(self::URL, $string);
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryData(): array
    {
        if ($this->queryData === null) {
            $data = $this->getData(self::QUERY_DATA) ?? [];

            if (!empty($data) && \is_array($data) === false) {
                $data = $this->serializer->unserialize($data);
            }

            $this->queryData = $data;
        }

        return $this->queryData;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueryData($data): WebhookInterface
    {
        if (is_array($data)) {
            $this->setData(self::QUERY_DATA, $this->serializer->serialize($data));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomOptions(): array
    {
        if ($this->customOptions === null) {
            $data = $this->getData(self::CUSTOM_OPTIONS) ?? [];

            if (\is_array($data) === false && !empty($data)) {
                try {
                    $data = $this->serializer->unserialize($data);
                } catch (\InvalidArgumentException $exception) {
                    $this->_logger->critical($exception->getMessage());
                }
            }

            $this->customOptions = $data;
        }

        return $this->customOptions;
    }

    /**
     * @param array $options
     *
     * @return WebhookInterface
     */
    public function setCustomOptions(array $options): WebhookInterface
    {
        // ISSUE-FIX #29: Custom option always require some nested data
        $this->helper->defineCustomOptionsMetaData($this, $options);

        return $this->setData(self::CUSTOM_OPTIONS, $this->serializer->serialize($options));
    }

    /**
     * @return array
     */
    public function getHeaderParams(): array
    {
        return $this->getQueryDataByType(QueryType::QUERY_PARAM_HEADER);
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getQueryDataByType($type): array
    {
        $data = $this->sortQueryData();
        return $data[$type] ?? [];
    }

    /**
     * @param array $mergeWith
     *
     * @return array
     */
    public function getHeaderUriParams(array $mergeWith = []): array
    {
        $params  = $this->getHeaderParams();
        $headers = [];

        foreach ($params as $header) {
            $headers[$header[self::HEADER_KEY_NAME]] = $header[self::HEADER_KEY_VALUE];
        }

        return array_merge($headers, $mergeWith);
    }

    /**
     * {@inheritDoc}
     */
    public function getOnlySignal(): int
    {
        return (int)$this->getData(self::ONLY_SIGNAL);
    }

    /**
     * @return bool
     */
    public function onlySignal(): bool
    {
        return (bool) $this->getOnlySignal();
    }

    /**
     * {@inheritDoc}
     */
    public function setOnlySignal($int): WebhookInterface
    {
        return $this->setData(self::ONLY_SIGNAL, (int) $int);
    }

    /**
     * @return array
     */
    protected function sortQueryData(): array
    {
        if (!\is_array($this->queryDataSort)) {
            $this->queryDataSort = [];

            foreach ($this->getQueryData() as $queryData) {
                if (\is_array($queryData) && \count($queryData) !== 0) {
                    foreach ($queryData as $dataItem) {
                        $this->queryDataSort[$dataItem['type']][] = $dataItem;
                    }
                }
            }
        }

        return $this->queryDataSort;
    }

    /**
     * Template method to return validate rules for the entity.
     *
     * @return Zend_Validate_Interface
     */
    protected function _getValidationRulesBeforeSave(): Zend_Validate_Interface
    {
        return $this->dataValidator;
    }
}
