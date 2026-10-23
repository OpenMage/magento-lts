<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product\Option\Type;

use Iterator;
use Mage;
use Mage_Catalog_Model_Product_Option_Type_File as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;

final class FileExtensionsTest extends OpenMageTest
{
    /**
     * The extensions a storefront custom option upload must refuse even when the
     * option has no allowed-extension list of its own.
     */
    public static function blockedExtensionProvider(): Iterator
    {
        yield 'php' => ['php'];
        yield 'php8' => ['php8'];
        yield 'phar' => ['phar'];
        yield 'phps' => ['phps'];
        yield 'phtml' => ['phtml'];
        yield 'html' => ['html'];
        yield 'exe' => ['exe'];
        yield 'svg' => ['svg'];
        yield 'svgz' => ['svgz'];
        yield 'xhtml' => ['xhtml'];
        yield 'xht' => ['xht'];
        yield 'xml' => ['xml'];
        yield 'xsl' => ['xsl'];
        yield 'xslt' => ['xslt'];
    }

    #[DataProvider('blockedExtensionProvider')]
    public function testUploadDenylistContains(string $extension): void
    {
        self::assertContains($extension, $this->denylist());
    }

    public function testUploadDenylistLeavesCommonTypesAlone(): void
    {
        $denylist = $this->denylist();
        foreach (['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'zip'] as $extension) {
            self::assertNotContains($extension, $denylist);
        }
    }

    /**
     * @return string[]
     */
    private function denylist(): array
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('catalog/product_option_type_file');

        return array_merge(
            (new ReflectionMethod(Subject::class, '_getProtectedFileExtensions'))->invoke($subject),
            (new ReflectionMethod(Subject::class, '_getForbiddenExtensions'))->invoke($subject),
        );
    }
}
