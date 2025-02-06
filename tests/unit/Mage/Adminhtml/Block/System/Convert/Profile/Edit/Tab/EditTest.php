<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Convert\Profile\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Edit as Subject;
use Mage_Dataflow_Model_Profile;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
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
