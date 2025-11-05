<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Model;

use Mage;
use Mage_Catalog_Model_Product;
use Mage_Checkout_Model_Cart;
use Mage_Core_Exception;
use Mage_Wishlist_Model_Item as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Wishlist\Model\ItemTrait;
use Varien_Object;

final class ItemTest extends OpenMageTest
{
    use ItemTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('wishlist/item');
    }

    /**
     * @group Model
     */
    public function testSetQty(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->setQty(1));
    }

    /**
     * @group Model
     */
    public function testSave(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->save());
    }

    /**
     * @dataProvider provideValidateData
     * @group Model
     * @throws Mage_Core_Exception
     */
    public function testValidate(?string $expectedExceptionMessage, ?int $wishlistId, ?int $productId): void
    {
        // TODO: mock methods
        self::$subject->setWishlistId($wishlistId);
        self::$subject->setProductId($productId);

        if ($expectedExceptionMessage) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $result = self::$subject->validate();

        if (!$expectedExceptionMessage) {
            self::assertTrue($result);
        }
    }

    /**
     * @group Model
     */
    public function testGetDataForSave(): void
    {
        self::assertIsArray(self::$subject->getDataForSave());
    }

    /**
     * @group Model
     */
    public function testLoadByProductWishlist(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->loadByProductWishlist(0, 0, []));
    }

    /**
     * @group Model
     */
    public function testGetProduct(): void
    {
        try {
            self::assertInstanceOf(Mage_Catalog_Model_Product::class, self::$subject->getProduct());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Cannot specify product.', $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testAddToCart(): void
    {
        $cart = new Mage_Checkout_Model_Cart();

        try {
            self::assertIsBool(self::$subject->addToCart($cart));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Cannot specify product.', $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testGetProductUrl(): void
    {
        try {
            self::assertIsString(self::$subject->getProductUrl());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Cannot specify product.', $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testGetBuyRequest(): void
    {
        self::assertInstanceOf(Varien_Object::class, self::$subject->getBuyRequest());
    }

    /**
     * @group Model
     */
    public function testMergeBuyRequest(): void
    {
        $buyRequest = new Varien_Object();

        self::assertInstanceOf(self::$subject::class, self::$subject->mergeBuyRequest($buyRequest));
    }

    /**
     * @group Model
     */
    public function testSetBuyRequest(): void
    {
        $buyRequest = new Varien_Object();

        self::assertInstanceOf(self::$subject::class, self::$subject->setBuyRequest($buyRequest));
    }

    /**
     * @group Model
     */
    public function testIsRepresent(): void
    {
        $product    = new Mage_Catalog_Model_Product();
        $buyRequest = new Varien_Object();

        self::assertIsBool(self::$subject->isRepresent($product, $buyRequest));
    }

    /**
     * @group Model
     */
    public function testRepresentProduct(): void
    {
        $product = new Mage_Catalog_Model_Product();

        try {
            self::assertIsBool(self::$subject->representProduct($product));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Cannot specify product.', $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testCompareOptions(): void
    {
        self::assertTrue(self::$subject->compareOptions([], []));
    }

    /**
     * @group Model
     */
    public function testSetOptions(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->setOptions([]));
    }

    /**
     * @group Model
     */
    public function testGetOptions(): void
    {
        self::assertIsArray(self::$subject->getOptions());
    }

    /**
     * @group Model
     */
    public function testGetOptionsByCode(): void
    {
        self::assertIsArray(self::$subject->getOptionsByCode());
    }

    /**
     * @group Model
     */
    public function testAddOption(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->addOption([]));
    }

    /**
     * @group Model
     */
    public function testRemoveOption(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->removeOption('invalid_code'));
    }

    /**
     * @group Model
     */
    public function testGetOptionByCode(): void
    {
        self::assertNull(self::$subject->getOptionByCode('invalid_code'));
    }

    /**
     * @group Model
     */
    public function testCanHaveQty(): void
    {
        try {
            self::assertIsBool(self::$subject->canHaveQty());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Cannot specify product.', $mageCoreException->getMessage());
        }
    }

    /**
     * @covers Mage_Wishlist_Model_Item::getCustomDownloadUrl()
     * @group Model
     */
    public function testGetCustomDownloadUrl(): void
    {
        self::assertIsString(self::$subject->getCustomDownloadUrl());
        self::assertStringContainsString('wishlist/index/downloadCustomOption', self::$subject->getCustomDownloadUrl());
    }

    /**
     * @covers Mage_Wishlist_Model_Item::setCustomDownloadUrl()
     * @group Model
     */
    public function testSetCustomDownloadUrl(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->setCustomDownloadUrl('test_url'));
    }

    /**
     * @covers Mage_Wishlist_Model_Item::getFileDownloadParams()
     * @group Model
     */
    public function testGetFileDownloadParams(): void
    {
        self::assertInstanceOf(Varien_Object::class, self::$subject->getFileDownloadParams());
    }

    /**
     * @group Model
     */
    public function testLoadWithOptions(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->loadWithOptions(1));
    }
}
