<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Model\Queue;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\MessageQueue\MergerInterface as MagentoMergerInterface;

/**
 * Class Merger
 *
 * @package MageHook\Hook\Model\Queue
 */
class MessageMerger implements MagentoMergerInterface
{
    public const TOPIC_NAME = 'async.webhook.http-request';

    /** @var DataObjectHelper $dataObjectHelper */
    protected $dataObjectHelper;

    /**
     * Merger constructor.
     *
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param array $messages
     * @return array|MagentoMergerInterface[]|object[]
     */
    public function merge(array $messages): array
    {
        return $messages;
    }
}
