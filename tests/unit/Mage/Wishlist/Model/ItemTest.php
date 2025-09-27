<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Wishlist_Model_Item as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ItemTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('wishlist/item');
    }

    /**
     * @dataProvider qtyDataProvider
     * @group Model
     */
    public function testSetQty(int $expectedQty, int $inputQty): void
    {
        self::$subject->setQty($inputQty);
        self::assertEquals($expectedQty, self::$subject->getQty());
    }

    public function qtyDataProvider(): \Generator
    {
        yield 'positive quantity' => [
            5,
            5,
        ];
        yield 'zero quantity' => [
            0,
            0,
        ];
        yield 'negative quantity' => [
            1,
            -1,
        ];
    }

    /**
     * @dataProvider validateDataProvider
     * @group Model
     * @throws Mage_Core_Exception
     */
    public function testValidate(?string $expectedExceptionMessage, ?int $wishlistId, ?int $productId): void
    {
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

    public function validateDataProvider(): \Generator
    {
        yield 'valid data' => [
            null,
            1,
            1,
        ];
        yield 'missing wishlist ID' => [
            'Cannot specify wishlist.',
            null,
            1,
        ];
        yield 'missing product ID' => [
            'Cannot specify product.',
            1,
            null,
        ];
    }
}
