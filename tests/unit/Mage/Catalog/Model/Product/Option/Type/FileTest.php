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
use Mage_Core_Exception;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\Option\Type\FileTrait;
use ReflectionMethod;

final class FileTest extends OpenMageTest
{
    use FileTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/product_option_type_file');
    }

    private function validate(string $path): void
    {
        (new ReflectionMethod(Subject::class, '_validateOptionFilePath'))
            ->invoke(self::$subject, $path);
    }

    public static function acceptedPathProvider(): Iterator
    {
        yield 'quote file' => ['/media/custom_options/quote/a/b/file.txt'];
        yield 'order file' => ['/media/custom_options/order/a/b/file.txt'];
        // Collapses to a path that is still inside the quote directory. The previous
        // regex denylist rejected this purely because it contained "..".
        yield 'dot segments inside root' => ['/media/custom_options/quote/../quote/file.txt'];
        yield 'single dot segments' => ['/media/custom_options/./quote/./file.txt'];
    }

    /**
     * @dataProvider acceptedPathProvider
     */
    public function testAcceptsPathsInsideTargetDirs(string $path): void
    {
        $this->validate($path);
        self::assertTrue(true, 'no exception expected');
    }

    public static function rejectedPathProvider(): Iterator
    {
        // Prefix that shares a name with the target dir but is a sibling of it.
        yield 'sibling sharing prefix' => ['/media/custom_options/quote_evil/shell.php'];
        yield 'no separator boundary' => ['/media/custom_options/quoteX'];
        yield 'traversal out of root' => ['/media/custom_options/quote/../../../etc/passwd'];
        yield 'traversal above webroot' => ['/../../etc/passwd'];
        yield 'backslash traversal' => ['\\media\\custom_options\\quote\\..\\..\\..\\etc\\passwd'];
        yield 'unrelated directory' => ['/media/other/file.txt'];
        yield 'target dir itself' => ['/media/custom_options/quote'];
        yield 'null byte' => ["/media/custom_options/quote/a\0.php"];
    }

    /**
     * @dataProvider rejectedPathProvider
     */
    public function testRejectsPathsOutsideTargetDirs(string $path): void
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Invalid file path.');
        $this->validate($path);
    }

    /**
     * @dataProvider acceptedPathProvider
     */
    public function testIsValidOptionFilePathAcceptsPathsInsideTargetDirs(string $path): void
    {
        self::assertTrue(self::$subject->isValidOptionFilePath($path));
    }

    /**
     * @dataProvider rejectedPathProvider
     */
    public function testIsValidOptionFilePathRejectsPathsOutsideTargetDirs(string $path): void
    {
        self::assertFalse(self::$subject->isValidOptionFilePath($path));
    }

    public static function nonStringPathProvider(): Iterator
    {
        yield 'missing key' => [null];
        yield 'empty string' => [''];
        yield 'array' => [['/media/custom_options/quote/a/b/file.txt']];
        yield 'integer' => [0];
        yield 'boolean' => [true];
    }

    /**
     * @dataProvider nonStringPathProvider
     */
    public function testIsValidOptionFilePathRejectsNonStrings(mixed $path): void
    {
        self::assertFalse(self::$subject->isValidOptionFilePath($path));
    }
}
