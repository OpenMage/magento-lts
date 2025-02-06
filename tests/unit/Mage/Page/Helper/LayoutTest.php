<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @covers Mage_Core_Helper_Abstract::isModuleEnabled()
     * @group Mage_Page
     * @group Mage_Page_Helper
     */
    public function testApplyTemplate(): void
    {
        $this->assertTrue($this->subject->isModuleEnabled());
    }
}
