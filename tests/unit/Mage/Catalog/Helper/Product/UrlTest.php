<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Catalog_Helper_Product_Url::getConvertTable()
 * @group Mage_Catalog
 * @group Mage_Catalog_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper\Product;

use Generator;
use Mage;
use Mage_Catalog_Helper_Product_Url as Subject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('catalog/product_url');
    }


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
