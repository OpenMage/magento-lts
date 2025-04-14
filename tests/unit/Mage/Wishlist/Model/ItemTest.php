<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Model;

use Mage;
use Mage_Wishlist_Model_Item as Subject;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /** @var Subject */
    private $subject;

    protected function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('wishlist/item');
    }

    /**
     * @dataProvider qtyDataProvider
     * @group Mage_Wishlist
     * @group Mage_Wishlist_Model
     */
    public function testSetQty(int $expectedQty, int $inputQty): void
    {
        $this->subject->setQty($inputQty);
        $this->assertEquals($expectedQty, $this->subject->getQty());
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
     * @group Mage_Wishlist
     * @group Mage_Wishlist_Model
     */
    public function testValidate(?string $expectedExceptionMessage, ?int $wishlistId, ?int $productId): void
    {
        $this->subject->setWishlistId($wishlistId);
        $this->subject->setProductId($productId);

        if ($expectedExceptionMessage) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $result = $this->subject->validate();

        if (!$expectedExceptionMessage) {
            $this->assertTrue($result);
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
