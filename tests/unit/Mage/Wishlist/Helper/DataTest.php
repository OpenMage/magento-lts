<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Helper;

use Mage;
use Mage_Catalog_Model_Product;
use Mage_Wishlist_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('wishlist');
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testgGetRemoveUrlCustom(): void
    {
        $item = new Mage_Catalog_Model_Product();
        $result = self::$subject->getRemoveUrlCustom($item);

        self::assertIsString($result);
        self::assertStringContainsString('wishlist/index/remove', $result);
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetAddToCartUrlCustom(): void
    {
        $item = 'some/url';
        $result = self::$subject->getAddToCartUrlCustom($item);

        self::assertIsString($result);
        self::assertStringContainsString($item, $result);
        self::assertStringContainsString('wishlist/index/cart', $result);
    }
}
