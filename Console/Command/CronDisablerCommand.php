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
            ->setDescription('Disables Magento entries in crontab.');

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
        $this->crontabManager->open();
        $this->crontabManager->disableJobs();
        $this->crontabManager->save();

        if ($this->crontabManager->getProcessedCount()) {
            $output->writeln("<info>Disabled {$this->crontabManager->getProcessedCount()} jobs from default crontab group.<info>");
        } else {
            $output->writeln("<info>Nothing to disable.<info>");
        }
    }
}