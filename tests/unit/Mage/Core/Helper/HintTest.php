<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Core_Helper_Hint::getAvailableHints()
 * @group Mage_Core
 * @group Mage_Core_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Hint as Subject;
use PHPUnit\Framework\TestCase;

class HintTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/hint');
    }

    
    public function testGetAvailableHints(): void
    {
        $this->assertSame([], $this->subject->getAvailableHints());
    }

    /**
     * @covers Mage_Core_Helper_Hint::getHintByCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHintByCode(): void
    {
        $this->assertNull($this->subject->getHintByCode('test'));
    }
}
