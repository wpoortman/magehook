<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Console\Command;

use MageHook\Hook\Api\Data\WebhookInterface;
use MageHook\Hook\Api\WebhookRepositoryInterface;
use MageHook\Hook\Helper\Events as EventsHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HookListCommand
 *
 * @package MageHook\Hook\Console\Command
 */
class HookListCommand extends Command
{
    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var WebhookRepositoryInterface $webhookRepository */
    protected $webhookRepository;

    /**
     * HookListCommand constructor.
     * @param EventsHelper $eventsHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WebhookRepositoryInterface $webhookRepository
     */
    public function __construct(
        EventsHelper $eventsHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WebhookRepositoryInterface $webhookRepository
    ) {
        $this->eventsHelper = $eventsHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->webhookRepository = $webhookRepository;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setName('magehook:hook:list')
             ->setDescription('Displays the list of web hooks');

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $table = new Table($output);
            $table->setHeaders(['ID', 'Active', 'Name', 'Event', 'URL']);

            /** @var SearchCriteriaInterface $events */
            $events = $this->webhookRepository->getList($this->searchCriteriaBuilder->create());

            /** @var WebhookInterface $hook */
            foreach ($events->getItems() as $hook) {
                $table->addRow([
                    $hook->getId(),
                    $hook->isActive() ? 'Yes' : 'No',
                    $hook->getName(),
                    $hook->getEvent(),
                    $hook->getUrl()
                ]);
            }

            $table->render();
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($exception->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        }

        return Cli::RETURN_SUCCESS;
    }
}
