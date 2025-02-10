<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Convert\Gui\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_View as Subject;
use Mage_Dataflow_Model_Profile;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }


    public function testInitForm(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getRegistryCurrentConvertProfile'])
            ->getMock();

        $mock
            ->method('getRegistryCurrentConvertProfile')
            ->willReturn(new Mage_Dataflow_Model_Profile());

        $this->assertInstanceOf(Subject::class, $mock->initForm());
    }
}
