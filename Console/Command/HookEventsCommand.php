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

use MageHook\Hook\Helper\Events;
use MageHook\Hook\Helper\Events as EventsHelper;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HookEventsCommand
 *
 * @package MageHook\Hook\Console\Command
 */
class HookEventsCommand extends Command
{
    /** @var EventsHelper $eventsHelper */
    protected $eventsHelper;

    /**
     * HookEventsCommand constructor.
     * @param EventsHelper $eventsHelper
     */
    public function __construct(
        EventsHelper $eventsHelper
    ) {
        $this->eventsHelper = $eventsHelper;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setName('magehook:hook:events')
             ->setDescription('List all available web hook events');

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $events = $this->eventsHelper->getList();

            $table = new Table($output);
            $table->setHeaders([
                'Name'
            ]);

            foreach ($events as $event) {
                $table->addRow([
                    $event[Events::EVENT_NAME]
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
}
