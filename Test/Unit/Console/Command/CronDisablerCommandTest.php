<?php
/**
 * Crontab disabler command tests
 *
 * @author    Wojtek Naruniec <wojtek@naruniec.me>
 * @copyright Wojtek Naruniec (c) 2018
 * @package   Wojtekn\CronManager
 */

namespace Wojtekn\CronManager\Test\Unit\Console\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Wojtekn\CronManager\Console\Command\CronDisablerCommand;

/**
 * Class CronDisablerCommandTest
 */
class CronDisablerCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CronDisablerCommand
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
            'Wojtekn\CronManager\Console\Command\CronDisablerCommand',
            [
                'crontabManager' => $this->crontabManagerMock,
            ]
        );
    }

    public function testExecuteMultipleDefaultGroup()
    {
        $this->crontabManagerMock->expects($this->exactly(2))->method('getProcessedCount')->willReturn(2);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        $this->assertEquals("Disabled 2 jobs from 'magento' crontab group.\n", $commandTester->getDisplay());
    }

    public function testExecuteMultipleCustomGroup()
    {
        $this->crontabManagerMock->expects($this->exactly(2))->method('getProcessedCount')->willReturn(2);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(['--group' => 'custom-group']);
        $this->assertEquals("Disabled 2 jobs from 'custom-group' crontab group.\n", $commandTester->getDisplay());
    }

    public function testExecuteNothing()
    {
        $this->crontabManagerMock->expects($this->once())->method('getProcessedCount')->willReturn(0);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        $this->assertEquals("Nothing to disable.\n", $commandTester->getDisplay());
    }
}
