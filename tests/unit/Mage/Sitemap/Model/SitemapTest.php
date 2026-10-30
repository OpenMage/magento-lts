<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model;

use Mage;
use Mage_Cms_Helper_Page;
use Mage_Sitemap_Model_Sitemap as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sitemap\SitemapTrait;
use ReflectionMethod;
use Throwable;

final class SitemapTest extends OpenMageTest
{
    use SitemapTrait;

    /**
     * @dataProvider provideGetPreparedFilenameData
     * @param array<string, string> $data
     * @group Model
     */
    public function testGetPreparedFilename(array $data): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, []);
        $mock->setData($data);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertIsString($mock->getPreparedFilename());
    }

    /**
     * The CMS home page is written as the base URL; the configured value may carry a page id ("home|2")
     *
     * @dataProvider provideGetHomePageIdentifierData
     * @group Model
     */
    public function testGetHomePageIdentifier(string $expected, string $configValue): void
    {
        $store = Mage::app()->getStore();
        $original = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
        $store->setConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE, $configValue);

        try {
            $method = new ReflectionMethod(Subject::class, 'getHomePageIdentifier');
            $identifier = $method->invoke($this->getMockWithCalledMethods(Subject::class, []), $store->getId());
        } finally {
            $store->setConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE, $original);
        }

        self::assertSame($expected, $identifier);
    }

    /**
     * @dataProvider provideGenerateXmlData
     * @param array<string, string> $data
     * @group Model
     * @throws Throwable
     * @todo  test validation
     * @todo  test content of xml
     */
    public function testGenerateXml(array $data): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, ['save' => null]);
        $mock->setData($data);
        self::assertInstanceOf(Subject::class, $mock);

        $result = $mock->generateXml();
        self::assertInstanceOf(Subject::class, $result);

        /** @var string $file */
        $file = $data['sitemap_filename'];
        self::assertFileExists($file);
        unlink($file);
    }
}
