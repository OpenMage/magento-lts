<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Helper;

use Mage;
use Mage_Page_Helper_Layout as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class LayoutTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('page/layout');
    }

    /**
     * @covers Mage_Core_Helper_Abstract::isModuleEnabled()
     * @group Helper
     */
    public function testIsModuleEnabled(): void
    {
        self::assertTrue(self::$subject->isModuleEnabled());
    }
}
