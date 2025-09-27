<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Mage;
use Mage_Catalog_Helper_Map as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class MapTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('catalog/map');
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetCategoryUrl(): void
    {
        static::assertStringEndsWith('/catalog/seo_sitemap/category/', self::$subject->getCategoryUrl());
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetProductUrl(): void
    {
        static::assertStringEndsWith('/catalog/seo_sitemap/product/', self::$subject->getProductUrl());
    }

    /**
     * @group Helper
     */
    public function testGetIsUseCategoryTreeMode(): void
    {
        static::assertIsBool(self::$subject->getIsUseCategoryTreeMode());
    }
}
