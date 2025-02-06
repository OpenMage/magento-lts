<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
