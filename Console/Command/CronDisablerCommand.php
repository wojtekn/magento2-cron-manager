<?php
/**
 * Command which disables crontab entries.
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 */

namespace Wojtekn\CronManager\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wojtekn\CronManager\Model\CrontabManager;

/**
 * Class CronDisablerCommand
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 */
class CronDisablerCommand extends Command
{
    /**
     * Group argument key
     */
    const GROUP_OPTION = 'group';

    /**
     * @var CrontabManager
     */
    private $crontabManager;

    /**
     * CronDisablerCommand constructor.
     * @param CrontabManager $crontabManager
     */
    public function __construct(CrontabManager $crontabManager)
    {
        $this->crontabManager = $crontabManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:crontab:disable')
            ->setDescription('Disables Magento entries in crontab.')
            ->setDefinition(
                [
                    new InputOption(
                        self::GROUP_OPTION,
                        '-g',
                        InputOption::VALUE_OPTIONAL,
                        'Disable crontab entries only from specified group.',
                        CrontabManager::DEFAULT_CRONTAB_GROUP
                    )
                ]
            );

        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getOption(self::GROUP_OPTION);

        $this->crontabManager->open();
        $this->crontabManager->disableJobs($group);
        $this->crontabManager->save();

        if ($this->crontabManager->getProcessedCount()) {
            $output->writeln("<info>Disabled {$this->crontabManager->getProcessedCount()} jobs from '{$group}' crontab group.<info>");
        } else {
            $output->writeln("<info>Nothing to disable.<info>");
        }
    }
}