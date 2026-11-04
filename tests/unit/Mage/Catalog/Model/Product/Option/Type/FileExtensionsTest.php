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
     * Extensions a storefront custom option upload refuses when the option has
     * no allowed-extension list of its own.
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
    public function testUnlistedOptionRefuses(string $extension): void
    {
        self::assertContains($extension, $this->uploadForbidden(null));
    }

    public function testUnlistedOptionLeavesCommonTypesAlone(): void
    {
        $forbidden = $this->uploadForbidden(null);
        foreach (['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'zip'] as $extension) {
            self::assertNotContains($extension, $forbidden);
        }
    }

    /**
     * An option that lists an active-content type keeps accepting it, as before the
     * GHSA-4fxg-469f-r6fc fix; script extensions stay refused.
     */
    public function testListedOptionKeepsActiveContentTypes(): void
    {
        $forbidden = $this->uploadForbidden(['svg', 'xml', 'png']);
        foreach (['svg', 'svgz', 'xml', 'xsl', 'xslt', 'xhtml', 'xht'] as $extension) {
            self::assertNotContains($extension, $forbidden);
        }

        foreach (['php', 'phar', 'phtml', 'exe'] as $extension) {
            self::assertContains($extension, $forbidden);
        }
    }

    /**
     * The configured catalog/custom_options/forbidden_extensions list applies whether or
     * not the option has an allowed-extension list.
     */
    public function testConfiguredForbiddenExtensionsAlwaysApply(): void
    {
        $subject = $this->createPartialMock(Subject::class, ['getConfigData']);
        $subject->method('getConfigData')->with('forbidden_extensions')->willReturn('exe, Foo');

        self::assertSame(
            ['exe', 'foo'],
            (new ReflectionMethod(Subject::class, '_getForbiddenExtensions'))->invoke($subject),
        );

        $method = new ReflectionMethod(Subject::class, '_getUploadForbiddenExtensions');
        foreach ([null, ['png']] as $allowed) {
            $forbidden = $method->invoke($subject, $allowed);
            self::assertIsArray($forbidden);
            self::assertContains('foo', $forbidden);
            self::assertSame(array_values(array_unique($forbidden)), $forbidden);
        }
    }

    /**
     * @param  null|string[] $allowed
     * @return string[]
     */
    private function uploadForbidden(?array $allowed): array
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('catalog/product_option_type_file');

        return (new ReflectionMethod(Subject::class, '_getUploadForbiddenExtensions'))->invoke($subject, $allowed);
    }
}
