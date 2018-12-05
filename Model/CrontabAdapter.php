<?php
/**
 * Simple crontab manager adapter
 *
 * @author    Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 * @package   Wojtekn\CronManager
 */

namespace Wojtekn\CronManager\Model;

/**
 * Class CrontabAdapter
 */
class CrontabAdapter
{
    /**
     * @var string Temp file path.
     */
    private $tmpFile = '';

    /**
     * @var string Crontab path.
     */
    private $command = '/usr/bin/crontab';

    /**
     * CrontabAdapter constructor.
     */
    public function __construct()
    {
        $this->tmpFile = $this->getDefaultTmpFilePath();
    }

    /**
     * Remove temp file.
     */
    public function __destruct()
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

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
        return $this->execute($this->command . ' ' . $this->tmpFile);
    }

    /**
     * Get path to file which is used to store crontab content for saving purpose.
     *
     * @return bool|string
     */
    public function getTmpFilePath()
    {
        return $this->tmpFile;
    }

    /**
     * Set path to file which is used to store crontab content for saving purpose.
     *
     * @param $tmpFile
     */
    public function setTmpFilePath($tmpFile)
    {
        $this->tmpFile = $tmpFile;
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

    /**
     * Generate temp file.
     *
     * @return bool|string
     */
    private function getDefaultTmpFilePath()
    {
        return tempnam(sys_get_temp_dir(), 'CRONTAB-MANAGER');
    }
}
