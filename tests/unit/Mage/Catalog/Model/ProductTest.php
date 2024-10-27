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

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Generator;
use Mage;
use Mage_Catalog_Model_Product;
use Mage_Catalog_Model_Product_Url;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public const TEST_STRING = 'a & B, x%, ä, ö, ü';

    public Mage_Catalog_Model_Product $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/product');
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetUrlModel(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Url::class, $this->subject->getUrlModel());
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testFormatUrlKey($expectedResult, ?string $locale): void
    {
        $this->subject->setLocale($locale);
        $this->assertSame($expectedResult, $this->subject->formatUrlKey(self::TEST_STRING));
    }

    public function provideFormatUrlKey(): Generator
    {
        yield 'null locale' => [
            'a-b-x-a-o-u',
            null,
        ];
        yield 'de_DE' => [
            'a-und-b-x-prozent-ae-oe-ue',
            'de_DE',
        ];
    }
}
