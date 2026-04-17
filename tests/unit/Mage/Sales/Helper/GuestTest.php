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
use Mage_Sales_Helper_Guest as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class GuestTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('sales/guest');
    }

    /**
     * @covers Mage_Sales_Helper_Guest::getCookieName()
     * @group Helper
     */
    public function testGetCookieName(): void
    {
        self::assertIsString(self::$subject->getCookieName());
    }
}
