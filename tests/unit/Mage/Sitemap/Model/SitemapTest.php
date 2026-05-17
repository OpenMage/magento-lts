<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Mage_Sitemap_Model_Sitemap as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sitemap\SitemapTrait;
use Throwable;

final class SitemapTest extends OpenMageTest
{
    use SitemapTrait;

    /**
     * @group Model
     */
    #[DataProvider('provideGetPreparedFilenameData')]
    public function testGetPreparedFilename(array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertIsString($mock->getPreparedFilename());
    }

    /**
     * @group Model
     * @throws Throwable
     * @todo  test validation
     * @todo  test content of xml
     */
    #[DataProvider('provideGenerateXmlData')]
    public function testGenerateXml(array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);
        self::assertInstanceOf(Subject::class, $mock);

        $result = $mock->generateXml();
        self::assertInstanceOf(Subject::class, $result);

        /** @var string $file */
        $file = $methods['getSitemapFilename'];
        self::assertFileExists($file);
        unlink($file);
    }
}
