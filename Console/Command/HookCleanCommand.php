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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HookCleanCommand
 *
 * @package MageHook\Hook\Console\Command
 */
class HookCleanCommand extends Command
{
    /**
     * Input options
     */
    public const INPUT_KEY_DRYRUN = 'dryrun';
    public const INPUT_KEY_DELETE = 'delete';

    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var WebhookRepositoryInterface $webhookRepository */
    protected $webhookRepository;

    /**
     * HookCleanCommand constructor.
     *
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
        $options = [
            new InputOption(
                self::INPUT_KEY_DRYRUN,
                null,
                InputOption::VALUE_NONE,
                'Dry run'
            ),
            new InputOption(
                self::INPUT_KEY_DELETE,
                null,
                InputOption::VALUE_NONE,
                'Delete'
            )
        ];

        $this->setName('magehook:hook:clean')
            ->setDescription('Removes all web hooks who are tied to non-existing events')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $config = $this->eventsHelper->getList();

            /** @var SearchCriteriaInterface $events */
            $events = $this->webhookRepository->getList($this->searchCriteriaBuilder
                ->addFilter(WebhookInterface::IS_ACTIVE, true)
                ->create());

            $compare = $this->compare($config, $events->getItems());

            if (\count($compare) === 0) {
                return $output->writeln('<info>No changes required</info>');
            }

            $table = new Table($output);
            $table->setHeaders([
                'ID', 'Name', 'Event', 'URL', 'Deactivated', 'Deleted'
            ]);

            /** @var WebhookInterface $hook */
            foreach ($compare as $hook) {
                // Predefine temporary data value
                $hook->setGotDeleted(false);
                $hook->setGotDeactivated(false);

                if ($input->getOption(self::INPUT_KEY_DELETE)) {
                    // Only delete the event if in non-dryrun mode
                    if (!$input->getOption(self::INPUT_KEY_DRYRUN)) {
                        $this->webhookRepository->delete($hook);
                    }

                    $hook->setGotDeleted(true);
                } else {
                    // Only deactivate the event if in non-dryrun mode
                    if (!$input->getOption(self::INPUT_KEY_DRYRUN)) {
                        $hook->setIsNotActive()->save();
                    }

                    $hook->setGotDeactivated(true);
                }

                $table->addRow([
                    $hook->getId(),
                    $hook->getName(),
                    $hook->getEvent(),
                    $hook->getUrl(),
                    $hook->getGotDeactivated() ? 'Yes' : 'No',
                    $hook->getGotDeleted() ? 'Yes' : 'No',
                ]);
            }

            $table->render();

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($exception->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * Compares all the existing hooks and the hooks that are configured
     * @param array $configEvents
     * @param array $repositoryItems
     * @return array
     */
    protected function compare(array $configEvents, array $repositoryItems): array
    {
        $items = [];

        foreach ($repositoryItems as $item) {
            $items[$item->getEvent()] = $item;
        }

        return \array_diff_key($items, $configEvents);
    }
}
