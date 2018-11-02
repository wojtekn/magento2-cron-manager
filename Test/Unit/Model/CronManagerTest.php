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

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->cronManager = $this->objectManager->getObject(
            'Wojtekn\CronManager\Model\CrontabManager'
        );
    }

    public function testValidate()
    {
        $this->setExpectedException('\Exception', 'No crontab groups found. Check if crontab was prepared to use with the tool.');
        $this->cronManager->validate();
    }
}