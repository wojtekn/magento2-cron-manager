<?php
/**
 * Crontab enabler command tests
 *
 * @package Wojtekn\CronManager
 * @author Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 */

namespace Wojtekn\CronManager\Test\Unit\Console\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Wojtekn\CronManager\Console\Command\CronEnablerCommand;

class CronEnablerCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CronEnablerCommand
     */
    private $command;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->crontabManagerMock = $this->getMockBuilder(\Wojtekn\CronManager\Model\CrontabManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->command = $this->objectManager->getObject(
            'Wojtekn\CronManager\Console\Command\CronEnablerCommand',
            [
                'crontabManager' => $this->crontabManagerMock,
            ]
        );
    }

    public function testExecuteMultiple()
    {
        $this->crontabManagerMock->expects($this->exactly(2))->method('getProcessedCount')->willReturn(3);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        $this->assertEquals("Enabled 3 jobs from default crontab group.\n", $commandTester->getDisplay());
    }

    public function testExecuteNothing()
    {
        $this->crontabManagerMock->expects($this->once())->method('getProcessedCount')->willReturn(0);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        $this->assertEquals("Nothing to enable.\n", $commandTester->getDisplay());
    }
}