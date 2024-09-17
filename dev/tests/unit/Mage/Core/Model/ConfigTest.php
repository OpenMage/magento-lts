<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    public Mage_Core_Model_Config $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/config');
    }

    public function testSaveDeleteGetConfig(): void
    {
        $path = 'test/config';
        $value = 'foo';

        $this->assertFalse($this->subject->getConfig($path));

        $this->subject->saveConfig($path, $value);
        $this->assertEquals($value, $this->subject->getConfig($path));

        $this->subject->deleteConfig($path);
        $this->assertFalse($this->subject->getConfig($path));
    }
}
