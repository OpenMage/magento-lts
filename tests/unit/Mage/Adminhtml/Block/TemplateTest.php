<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @see Mage_Core_Model_Session::getFormKey()
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Block
 * @group runInSeparateProcess
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block;

use Mage;
use Mage_Adminhtml_Block_Template as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    use CoreTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    
    public function testGetFormKey(): void
    {
        $this->assertIsString($this->subject->getFormKey());
    }

    /**
     * @covers Mage_Adminhtml_Block_Template::isOutputEnabled()
     * @dataProvider provideIsOutputEnabled
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testIsOutputEnabled(bool $expectedResult, ?string $moduleName): void
    {
        $this->assertSame($expectedResult, $this->subject->isOutputEnabled($moduleName));
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testGetModuleName(): void
    {
        $this->assertSame('Mage_Adminhtml', $this->subject->getModuleName());
    }

    /**
     * @see Mage_Core_Model_Input_Filter_MaliciousCode::filter()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testMaliciousCodeFilter(): void
    {
        $this->assertIsString($this->subject->maliciousCodeFilter(''));
    }
}
