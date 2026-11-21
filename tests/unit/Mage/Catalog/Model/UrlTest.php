<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Override;
use Mage;
use Mage_Catalog_Model_Resource_Url;
use Mage_Catalog_Model_Url as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\IntOrNullTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\UrlTrait;
use Varien_Object;

final class UrlTest extends OpenMageTest
{
    use CatalogTrait;

    use IntOrNullTrait;

    use UrlTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/url');
    }

    /**
     * @group Model
     */
    public function testGetStoreRootCategory(): void
    {
        self::assertInstanceOf(Varien_Object::class, self::$subject->getStoreRootCategory(1));
    }

    /**
     * @dataProvider provideIntOrNull
     * @group Model
     */
    public function testRefreshRewrites(?int $storeId): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->refreshRewrites($storeId));
    }

    /**
     * @dataProvider provideGeneratePathData
     *
     * @group Model
     */
    public function testGeneratePath(
        string $expectedResult,
        string $type,
        ?Varien_Object $product,
        ?Varien_Object $category,
        ?string $parentPath = null
    ): void {
        try {
            self::assertSame($expectedResult, self::$subject->generatePath($type, $product, $category, $parentPath));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame($expectedResult, $mageCoreException->getMessage());
        }
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Model
     */
    public function testFormatUrlKey(string $expectedResult, string $locale): void
    {
        self::$subject->setLocale($locale);
        self::assertSame($expectedResult, self::$subject->formatUrlKey($this->getTestString()));
    }

    /**
     * @group Model
     * @doesNotPerformAssertions
     */
    //    public function testGetSlugger(): void
    //    {
    //        self::$subject->getSlugger();
    //    }

    /**
     * @dataProvider provideGetSluggerConfig
     * @group Model
     */
    public function testGetSluggerConfig(array $expectedResult, string $locale): void
    {
        $result = self::$subject->getSluggerConfig($locale);

        self::assertArrayHasKey($locale, $result);

        self::assertArrayHasKey('%', $result[$locale]);
        self::assertArrayHasKey('&', $result[$locale]);

        self::assertSame($expectedResult[$locale]['%'], $result[$locale]['%']);
        self::assertSame($expectedResult[$locale]['&'], $result[$locale]['&']);

        self::assertSame('at', $result[$locale]['@']);
    }

    /**
     * @group Model
     */
    public function testGetCategoryRequestPathReclaimsOldPathWithSuffix(): void
    {
        $resource = $this->createMock(Mage_Catalog_Model_Resource_Url::class);
        $resource->method('getCategoryModel')->willReturn(Mage::getModel('catalog/category'));
        $resource->method('findFinalTargetPath')->with('parent/child.html', 1)->willReturn('category/5');
        $resource->expects(self::once())->method('deleteRewriteRecord')
            ->willReturnCallback(static function (string $requestPath, int $storeId, bool $permanent = false): void {
                self::assertSame('parent/child.html', $requestPath);
                self::assertSame(1, $storeId);
                self::assertFalse($permanent, 'A rewrite that is not a permanent redirect must be removed as well.');
            });

        $subject = $this->createPartialMock(Subject::class, ['getCategoryUrlSuffix', 'getResource']);
        $subject->method('getCategoryUrlSuffix')->willReturn('.html');
        $subject->method('getResource')->willReturn($resource);

        $category = new Varien_Object(['id' => 5, 'store_id' => 1, 'url_key' => 'child']);

        self::assertSame('parent/child.html', $subject->getCategoryRequestPath($category, 'parent/'));
    }
}
