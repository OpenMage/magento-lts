<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Core_Helper_Abstract::isModuleEnabled()
 * @group Mage_Page
 * @group Mage_Page_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Helper;

use Mage;
use Mage_Page_Helper_Layout as Subject;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('page/layout');
    }


    public function testApplyTemplate(): void
    {
        $this->assertTrue($this->subject->isModuleEnabled());
    }
}
