<?php
/**
 * Crontab manager tests
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 */

namespace Wojtekn\CronManager\Test\Unit\Model;

class CronManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Wojtekn\CronManager\Model\CrontabManager
     */
    private $cronManager;

    /**
     * @var \Wojtekn\CronManager\Model\CrontabAdapter
     */
    private $cronAdapter;

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->cronAdapter = $this->getMockBuilder('Wojtekn\CronManager\Model\CrontabAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'save'])
            ->getMock();

        $this->cronManager = $this->objectManager->getObject(
            'Wojtekn\CronManager\Model\CrontabManager',
            ['crontabAdapter' => $this->cronAdapter]
        );
    }

    public function testValidateThrowsException()
    {
        $this->setExpectedException('\Exception', 'No crontab groups found. Check if crontab was prepared to use with the tool.');
        $this->cronManager->validate();
    }

    public function testOpensParsesCrontabAndEnablesSingleJob()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:magento]" . PHP_EOL .
            "#* * * * * php bin/magento cron:run" . PHP_EOL .
            "# [end:magento]"
        );
        $this->cronManager->open();
        $this->cronManager->enableJobs();

        $this->assertEquals(1, $this->cronManager->getProcessedCount());
    }

    public function testOpensParsesCrontabAndDisablesSingleJob()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:magento]" . PHP_EOL .
            "* * * * * php bin/magento cron:run" . PHP_EOL .
            "# [end:magento]"
        );
        $this->cronManager->open();
        $this->cronManager->disableJobs();

        $this->assertEquals(1, $this->cronManager->getProcessedCount());
    }

    public function testOpensParsesCrontabAndEnablesMultipleJobs()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:magento]" . PHP_EOL .
            "#* * * * * php bin/magento cron:run --group=index" . PHP_EOL .
            "#* * * * * php bin/magento cron:run --group=mailchimp" . PHP_EOL .
            "# [end:magento]"
        );
        $this->cronManager->open();
        $this->cronManager->enableJobs();

        $this->assertEquals(2, $this->cronManager->getProcessedCount());
    }

    public function testOpensParsesCrontabAndEnablesMultipleJobsFromCustomGroup()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:my-group]" . PHP_EOL .
            "#* * * * * php bin/magento cron:run --group=index" . PHP_EOL .
            "#* * * * * php bin/magento cron:run --group=mailchimp" . PHP_EOL .
            "# [end:my-group]"
        );
        $this->cronManager->open();
        $this->cronManager->enableJobs('my-group');

        $this->assertEquals(2, $this->cronManager->getProcessedCount());
    }

    public function testOpensParsesCrontabAndDisablesMultipleJobs()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:magento]" . PHP_EOL .
            "* * * * * php bin/magento cron:run --group=index" . PHP_EOL .
            "* * * * * php bin/magento cron:run --group=mailchimp" . PHP_EOL .
            "# [end:magento]"
        );
        $this->cronManager->open();
        $this->cronManager->disableJobs();

        $this->assertEquals(2, $this->cronManager->getProcessedCount());
    }

    public function testOpensParsesCrontabAndDisablesMultipleJobsFromCustomGroup()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:my-group]" . PHP_EOL .
            "* * * * * php bin/magento cron:run --group=index" . PHP_EOL .
            "* * * * * php bin/magento cron:run --group=mailchimp" . PHP_EOL .
            "# [end:my-group]"
        );
        $this->cronManager->open();
        $this->cronManager->disableJobs('my-group');

        $this->assertEquals(2, $this->cronManager->getProcessedCount());
    }

    public function testPreparesCrontabContentForWriting()
    {
        $this->cronAdapter->expects($this->once())->method('load')->willReturn(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:magento]" . PHP_EOL .
            "* * * * * php bin/magento cron:run --group=index" . PHP_EOL .
            "* * * * * php bin/magento cron:run --group=mailchimp" . PHP_EOL .
            "# [end:magento]"
        );
        $this->cronManager->open();
        $this->cronManager->disableJobs();

        $this->assertEquals(
            "MAILTO=johndoe@example.com" . PHP_EOL .
            "# [start:magento]" . PHP_EOL .
            "#* * * * * php bin/magento cron:run --group=index" . PHP_EOL .
            "#* * * * * php bin/magento cron:run --group=mailchimp" . PHP_EOL .
            "# [end:magento]",
            $this->cronManager->prepareContent()
        );
    }
}