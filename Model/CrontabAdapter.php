<?php
/**
 * Simple crontab manager adapter
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 */

namespace Wojtekn\CronManager\Model;

/**
 * Class CrontabAdapter
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 */
class CrontabAdapter
{
    /**
     * @var string Temp file path.
     */
    private $tmpFile = '/tmp/crontab.txt';

    /**
     * @var string Crontab path.
     */
    private $command = '/usr/bin/crontab';

    /**
     * Load crontab content
     */
    public function load()
    {
        return $this->execute($this->command . ' -l');
    }

    /**
     * Save crontab content using temp file
     *
     * @param $fileContent
     * @return null|string
     */
    public function save($fileContent)
    {
        file_put_contents($this->tmpFile, $fileContent);
        return $this->execute($this->command . ' ' . $tmpFile);
    }

    /**
     * Executes command
     *
     * @param string $command Command to execute.
     * @return null|string
     */
    private function execute($command)
    {
        $output = shell_exec($command);
        return $output;
    }
}