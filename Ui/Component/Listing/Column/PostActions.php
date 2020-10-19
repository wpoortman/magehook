<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Ui\Component\Listing\Column;

use MageHook\Hook\Api\Data\WebhookInterface;
use Magento\Framework\App\State as ApplicationState;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column as UiCoreColumn;

/**
 * Class PostActions
 *
 * @package MageHook\Hook\Ui\Component\Listing\Column
 */
class PostActions extends UiCoreColumn
{
    public const URL_PATH_EDIT = 'webhook/grid/edit';
    public const URL_PATH_PING = 'webhook/grid/ping';
    public const URL_PATH_DELETE = 'webhook/grid/delete';

    /** @var UrlInterface $urlBuilder */
    protected $urlBuilder;

    /** @var ApplicationState $applicationState */
    protected $applicationState;

    /**
     * PostActions constructor.
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ApplicationState $applicationState
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ApplicationState $applicationState,
        $components = [],
        $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->applicationState = $applicationState;

        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[WebhookInterface::ID])) {
                    $items = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    WebhookInterface::ID => $item[WebhookInterface::ID]
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'ping' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_PING,
                                [
                                    WebhookInterface::EVENT => $item[WebhookInterface::EVENT]
                                ]
                            ),
                            'label' => __('Ping')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    WebhookInterface::ID => $item[WebhookInterface::ID]
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete ${ $.$data.name }'),
                                'message' => __('Are you sure you want to delete the webhook: ${ $.$data.name }?')
                            ]
                        ]
                    ];

                    if ((int)$item[WebhookInterface::IS_ACTIVE] === WebhookInterface::STATUS_CONCEPT) {
                        unset($items['ping']);

                        // Overwrite modal credentials for Concept webhooks with (optional) an empty name
                        $items['delete']['confirm']['title'] = __('Delete concept');
                        $items['delete']['confirm']['message'] = __('Are you sure you want to delete this concept');
                    }

                    $item[$this->getData('name')] = $items;
                }
            }
        }

        return $dataSource;
    }
}
