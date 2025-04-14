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

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Generator;
use Mage;
use Mage_Catalog_Helper_Data as Subject;
use Mage_Catalog_Model_Template_Filter;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::helper('catalog');
    }

    /**
     * @dataProvider provideSplitSku
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testSplitSku(array $expectedResult, string $sku, int $length = 30): void
    {
        static::assertSame($expectedResult, self::$subject->splitSku($sku, $length));
    }

    public function provideSplitSku(): Generator
    {
        yield 'test #1' => [
            [
                '100',
            ],
            '100',
        ];
        yield 'test #2 w/ length' => [
            [
                '10',
                '0',
            ],
            '100',
            2,
        ];
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testShouldSaveUrlRewritesHistory(): void
    {
        static::assertIsBool(self::$subject->shouldSaveUrlRewritesHistory());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testIsUsingStaticUrlsAllowed(): void
    {
        static::assertIsBool(self::$subject->isUsingStaticUrlsAllowed());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testIsUrlDirectivesParsingAllowed(): void
    {
        static::assertIsBool(self::$subject->isUrlDirectivesParsingAllowed());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetPageTemplateProcessor(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Template_Filter::class, self::$subject->getPageTemplateProcessor());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetOldFieldMap(): void
    {
        static::assertSame([], self::$subject->getOldFieldMap());
    }
}
