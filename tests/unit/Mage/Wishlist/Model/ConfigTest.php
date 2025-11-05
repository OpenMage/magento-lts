<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Model;

use Mage;
use Mage_Wishlist_Model_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ConfigTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('wishlist/config');
    }

    /**
     * @covers Mage_Wishlist_Model_Config::getProductAttributes()
     * @group Model
     */
    public function testGetProductAttributes(): void
    {
        self::assertIsArray(self::$subject->getProductAttributes());
    }
}
