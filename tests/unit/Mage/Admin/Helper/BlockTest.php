<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Admin_Helper_Block::isTypeAllowed()
 * @group Mage_Admin
 * @group Mage_Admin_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Helper;

use Mage;
use Mage_Admin_Helper_Block as Subject;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('admin/block');
    }


    public function testIsTypeAllowed(): void
    {
        $this->assertFalse($this->subject->isTypeAllowed('some-type'));
    }

    /**
     * @covers Mage_Admin_Helper_Block::getDisallowedBlockNames()
     * @group Mage_Admin
     * @group Mage_Admin_Helper
     */
    public function testGetDisallowedBlockNames(): void
    {
        $this->assertSame(['install/end'], $this->subject->getDisallowedBlockNames());
    }
}
