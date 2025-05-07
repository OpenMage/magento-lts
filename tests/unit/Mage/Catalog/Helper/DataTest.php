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

use Mage;
use Mage_Catalog_Helper_Data as Subject;
use Mage_Catalog_Model_Template_Filter;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper\DataTrait;

class DataTest extends OpenMageTest
{
    use DataTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('catalog');
    }

    /**
     * @dataProvider provideSplitSku
     * @group Helper
     */
    public function testSplitSku(array $expectedResult, string $sku, int $length = 30): void
    {
        static::assertSame($expectedResult, self::$subject->splitSku($sku, $length));
    }

    /**
     * @group Helper
     */
    public function testShouldSaveUrlRewritesHistory(): void
    {
        static::assertIsBool(self::$subject->shouldSaveUrlRewritesHistory());
    }

    /**
     * @group Helper
     */
    public function testIsUsingStaticUrlsAllowed(): void
    {
        static::assertIsBool(self::$subject->isUsingStaticUrlsAllowed());
    }

    /**
     * @group Helper
     */
    public function testIsUrlDirectivesParsingAllowed(): void
    {
        static::assertIsBool(self::$subject->isUrlDirectivesParsingAllowed());
    }

    /**
     * @group Helper
     */
    public function testGetPageTemplateProcessor(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Template_Filter::class, self::$subject->getPageTemplateProcessor());
    }

    /**
     * @group Helper
     */
    public function testGetOldFieldMap(): void
    {
        static::assertSame([], self::$subject->getOldFieldMap());
    }
}
