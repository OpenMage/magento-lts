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

use Generator;
use Mage;
use Mage_Catalog_Helper_Product_Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public Mage_Catalog_Helper_Product_Url $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('catalog/product_url');
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::getConvertTable()
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetConvertTable(): void
    {
        $result = $this->subject->getConvertTable();
        $this->assertCount(317, $result);
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::getConvertTableCustom()
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetConvertTableCustom(): void
    {
        $result = $this->subject->getConvertTableCustom();
        $this->assertCount(0, $result);
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::getConvertTableShort()
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetConvertTableShort(): void
    {
        $result = $this->subject->getConvertTableShort();
        $this->assertCount(4, $result);
    }

    /**
     * @covers Mage_Catalog_Helper_Product_Url::format()
     * @dataProvider provideFormat
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testFormat(string $expectedResult, ?string $string): void
    {
        $this->assertSame($expectedResult, $this->subject->format($string));
    }

    public function provideFormat(): Generator
    {
        yield 'null' => [
            '',
            null,
        ];
        yield 'string' => [
            'string',
            'string',
        ];
        yield 'umlauts' => [
            'string with aou',
            'string with ÄÖÜ',
        ];
        yield 'at' => [
            'at',
            '@',
        ];
    }
}
