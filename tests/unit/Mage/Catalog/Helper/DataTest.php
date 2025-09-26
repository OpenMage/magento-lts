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
use Mage_Catalog_Helper_Data as Subject;
use Mage_Catalog_Model_Template_Filter;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper\DataTrait;

final class DataTest extends OpenMageTest
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
        self::assertSame($expectedResult, self::$subject->splitSku($sku, $length));
    }

    /**
     * @group Helper
     */
    public function testShouldSaveUrlRewritesHistory(): void
    {
        self::assertIsBool(self::$subject->shouldSaveUrlRewritesHistory());
    }

    /**
     * @group Helper
     */
    public function testIsUsingStaticUrlsAllowed(): void
    {
        self::assertIsBool(self::$subject->isUsingStaticUrlsAllowed());
    }

    /**
     * @group Helper
     */
    public function testIsUrlDirectivesParsingAllowed(): void
    {
        self::assertIsBool(self::$subject->isUrlDirectivesParsingAllowed());
    }

    /**
     * @group Helper
     */
    public function testGetPageTemplateProcessor(): void
    {
        self::assertInstanceOf(Mage_Catalog_Model_Template_Filter::class, self::$subject->getPageTemplateProcessor());
    }

    /**
     * @group Helper
     */
    public function testGetOldFieldMap(): void
    {
        self::assertSame([], self::$subject->getOldFieldMap());
    }
}
