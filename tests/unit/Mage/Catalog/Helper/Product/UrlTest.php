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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper\Product;

use Mage;
use Mage_Catalog_Helper_Product_Url as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper\Product\UrlTrait;

class UrlTest extends OpenMageTest
{
    use UrlTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('catalog/product_url');
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::getConvertTable()
     * @group Helper
     */
    public function testGetConvertTable(): void
    {
        $result = self::$subject->getConvertTable();
        static::assertCount(317, $result);
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::getConvertTableCustom()
     * @group Helper
     */
    public function testGetConvertTableCustom(): void
    {
        $result = self::$subject->getConvertTableCustom();
        static::assertEmpty($result);
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::getConvertTableShort()
     * @group Helper
     */
    public function testGetConvertTableShort(): void
    {
        $result = self::$subject->getConvertTableShort();
        static::assertCount(4, $result);
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::format()
     * @dataProvider provideFormat
     * @group Helper
     */
    public function testFormat(string $expectedResult, ?string $string): void
    {
        static::assertSame($expectedResult, self::$subject->format($string));
    }
}
