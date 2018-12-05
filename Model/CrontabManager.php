<?php
/**
 * Simple crontab manager
 *
 * @author    Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 * @package   Wojtekn\CronManager
 */

namespace Wojtekn\CronManager\Model;

/**
 * Class CrontabManager
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
    const TAG_START = '/\[start\:(.*?)\]/';
    const TAG_END = '/\[end\:(.*?)\]/';

    /**
     * Job record keys
     */
    const KEY_CONTENT = 'content';
    const KEY_GROUP   = 'group';

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
     * @var \Wojtekn\CronManager\Model\CrontabAdapter
     */
    private $crontabAdapter;

    /**
     * CrontabManager constructor.
     * @param CrontabAdapter $crontabAdapter
     */
    public function __construct(CrontabAdapter $crontabAdapter)
    {
        $this->crontabAdapter = $crontabAdapter;
    }

    /**
     * Reads and parses crontab entries.
     */
    public function open()
    {
        $output = $this->crontabAdapter->load();

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
        if (!$this->getProcessedCount()) {
            return $this;
        }

        $fileContent = $this->prepareContent();
        $this->crontabAdapter->save($fileContent);

        return $this;
    }

    /**
     * Returns count of processed items.
     *
     * @return integer
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
            if (preg_match(self::TAG_START, $line, $matches) == 1) {
                $lines[] = [
                    self::KEY_CONTENT => trim($line),
                    self::KEY_GROUP => null,
                ];
                $currentGroup = $matches[1];
                $this->groups[] = $currentGroup;
                continue;
            }

            if (preg_match(self::TAG_END, $line, $matches) == 1) {
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
    public function prepareContent()
    {
        return implode(
            PHP_EOL,
            array_column($this->lines, self::KEY_CONTENT)
        );
    }
}
