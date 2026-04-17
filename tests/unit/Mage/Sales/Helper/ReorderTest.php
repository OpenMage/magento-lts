<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Helper;

use Mage;
use Mage_Sales_Helper_Reorder as Subject;
use Mage_Sales_Model_Order;
use OpenMage\Tests\Unit\OpenMageTest;

final class ReorderTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('sales/reorder');
    }

    /**
     * @covers Mage_Sales_Helper_Reorder::isAllow()
     * @group Helper
     */
    public function testIsAllow(): void
    {
        self::assertIsBool(self::$subject->isAllow());
    }

    /**
     * @covers Mage_Sales_Helper_Reorder::isAllowed()
     * @group Helper
     */
    public function testIsAllowed(): void
    {
        self::assertIsBool(self::$subject->isAllowed());
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testCanReorder(): void
    {
        self::assertIsBool(self::$subject->canReorder(new Mage_Sales_Model_Order()));
    }
}
