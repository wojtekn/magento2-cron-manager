<?php
/**
 * Simple crontab manager
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 */

namespace Wojtekn\CronManager\Model;

/**
 * Class CrontabManager
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 */
class CrontabManager
{
    /**
     * Default crontab group.
     */
    const DEFAULT_CRONTAB_GROUP = 'magento';

    /**
     * Start and end tags
     */
    const TAG_START = '[start:magento]';
    const TAG_END = '[end:magento]';

    /**
     * Job record keys
     */
    const KEY_CONTENT = 'content';
    const KEY_GROUP   = 'group';

    /**
     * @var string Crontab path.
     */
    private $command = '/usr/bin/crontab';

    /**
     * @var string Temp file path.
     */
    private $tmpFile = '/tmp/crontab.txt';

    /**
     * Parsed crontab lines.
     *
     * @var array
     */
    private $lines;

    /**
     * Crontab groups avalable in parsed file.
     *
     * @var array
     */
    private $groups = [];

    /**
     * @var int Count of processed items
     */
    private $processedCount = 0;

    /**
     * Reads and parses crontab entries.
     */
    public function open()
    {
        $output = $this->execute($this->command . ' -l');

        $this->lines = $this->parseCrontab($output);
    }

    /**
     * Enables all jobs from provided group
     *
     * @param string $crontabGroup Crontab jobs group to enable
     * @return $this
     * @throws \Exception
     */
    public function enableJobs($crontabGroup = self::DEFAULT_CRONTAB_GROUP)
    {
        $this->validate();

        foreach ($this->lines as $index => $line) {
            if ($line[self::KEY_GROUP] !== $crontabGroup) continue;

            if (strpos($line[self::KEY_CONTENT], '#') === 0) {
                $this->lines[$index][self::KEY_CONTENT] = ltrim($line[self::KEY_CONTENT], "#");;
                $this->processedCount++;
            }
        }

        return $this;
    }

    /**
     * Disables all jobs from provided group
     *
     * @param string $crontabGroup Crontab jobs group to disable
     * @return $this
     * @throws \Exception
     */
    public function disableJobs($crontabGroup = self::DEFAULT_CRONTAB_GROUP)
    {
        $this->validate();

        foreach ($this->lines as $index => $line) {
            if ($line[self::KEY_GROUP] !== $crontabGroup) continue;

            if (strpos($line[self::KEY_CONTENT], '#') === false) {
                $this->lines[$index][self::KEY_CONTENT] = '#' . $line[self::KEY_CONTENT];
                $this->processedCount++;
            }
        }

        return $this;
    }

    /**
     * Saves generated crontab content into file.
     *
     * @return $this
     */
    public function save()
    {
        $fileContent = $this->prepareContent();
        file_put_contents($this->tmpFile, $fileContent);
        shell_exec($this->command . ' ' . $this->tmpFile);

        return $this;
    }

    /**
     * Returns count of processed items.
     *
     * @return int
     */
    public function getProcessedCount()
    {
        return $this->processedCount;
    }

    /**
     * Crontab format validation.
     *
     * @throws \Exception
     */
    public function validate()
    {
        if (!count($this->groups)) {
            throw new \Exception(
                'No crontab groups found. Check if crontab was prepared to use with the tool.'
            );
        }
    }

    /**
     * Parses crontab contents into array.
     *
     * @param string $output Generated output ready to save into crontab file.
     * @return array|string
     */
    private function parseCrontab($output)
    {
        $lines = [];
        $output = explode(PHP_EOL, $output);
        $currentGroup = null;

        foreach ($output as $line) {
            if (strpos($line, self::TAG_START) !== false) {
                $lines[] = [
                    self::KEY_CONTENT => trim($line),
                    self::KEY_GROUP => null,
                ];
                $currentGroup = self::DEFAULT_CRONTAB_GROUP;
                $this->groups[] = $currentGroup;
                continue;
            }

            if (strpos($line, self::TAG_END) !== false) {
                $lines[] = [
                    self::KEY_CONTENT => trim($line),
                    self::KEY_GROUP => null,
                ];
                $currentGroup = null;
                continue;
            }

            $lines[] = [
                self::KEY_CONTENT => trim($line),
                self::KEY_GROUP   => $currentGroup,
            ];
        }

        return $lines;
    }

    /**
     * Prepares crontab file content for writing.
     *
     * @return string
     */
    private function prepareContent()
    {
        return implode(
            PHP_EOL,
            array_column($this->lines, self::KEY_CONTENT)
        );
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