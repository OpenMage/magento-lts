<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Core
 * @group Mage_Core_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Config as Subject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public Subject $subject;

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
        $this->assertSame($value, $this->subject->getConfig($path));

        $this->subject->deleteConfig($path);
        $this->assertFalse($this->subject->getConfig($path));
    }
}
