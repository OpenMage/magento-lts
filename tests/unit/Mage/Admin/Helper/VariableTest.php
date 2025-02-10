<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Admin_Helper_Variable::isPathAllowed()
 * @group Mage_Admin
 * @group Mage_Admin_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Helper;

use Mage;
use Mage_Admin_Helper_Variable as Subject;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('admin/variable');
    }


    public function testIsPathAllowed(): void
    {
        $this->assertIsBool($this->subject->isPathAllowed(''));
    }
}
