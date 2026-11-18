<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Override;
use Mage;
use Mage_Core_Exception;
use Mage_Downloadable_Helper_File as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\DownloadableTrait;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Io_File;

final class FileTest extends OpenMageTest
{
    use DownloadableTrait;

    private static Subject $subject;

    private string $root = '';

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('downloadable/file');
    }

    protected function tearDown(): void
    {
        if ($this->root !== '') {
            (new Varien_Io_File())->rmdir($this->root, true);
            $this->root = '';
        }

        parent::tearDown();
    }

    /**
     * @dataProvider provideGetFilePathData
     *
     * @group Helper
     */
    public function testGetFilePath(string $expectedResult, string $path, ?string $file): void
    {
        $result = self::$subject->getFilePath($path, $file);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider provideIsValidRelativeFilePathData
     *
     * @group Helper
     */
    public function testIsValidRelativeFilePath(bool $expectedResult, mixed $file): void
    {
        self::assertSame($expectedResult, self::$subject->isValidRelativeFilePath($file));
    }

    /**
     * @group Helper
     */
    public function testMoveFileFromTmpMovesUploadedFile(): void
    {
        [$tmpPath, $basePath] = $this->createDirectories();
        mkdir($tmpPath . DS . 'r' . DS . 'e', 0777, true);
        file_put_contents($tmpPath . DS . 'r' . DS . 'e' . DS . 'readme.txt', 'sample');

        $result = self::$subject->moveFileFromTmp($tmpPath, $basePath, [
            ['file' => '/r/e/readme.txt', 'status' => 'new'],
        ]);

        self::assertSame('/r/e/readme.txt', $result);
        self::assertFileExists($basePath . DS . 'r' . DS . 'e' . DS . 'readme.txt');
        self::assertFileDoesNotExist($tmpPath . DS . 'r' . DS . 'e' . DS . 'readme.txt');
    }

    /**
     * The tmp and the target directory have the same depth, so the traversal resolves to the
     * same file on both sides and the move would rename it in place to "_1.txt".
     *
     * @dataProvider provideMoveFileFromTmpTraversalData
     *
     * @group Helper
     */
    public function testMoveFileFromTmpRejectsTraversal(string $file, string $status): void
    {
        [$tmpPath, $basePath] = $this->createDirectories();
        $protected = $this->root . DS . 'protected.txt';
        file_put_contents($protected, 'secret');

        $this->expectException(Mage_Core_Exception::class);

        try {
            self::$subject->moveFileFromTmp($tmpPath, $basePath, [
                ['file' => $file, 'status' => $status],
            ]);
        } finally {
            self::assertFileExists($protected);
            self::assertFileDoesNotExist($this->root . DS . 'protected_1.txt');
        }
    }

    /**
     * @return array{string, string}
     */
    private function createDirectories(): array
    {
        $this->root = sys_get_temp_dir() . DS . 'openmage-downloadable-' . bin2hex(random_bytes(6));
        $tmpPath = $this->root . DS . 'tmp' . DS . 'samples';
        $basePath = $this->root . DS . 'files' . DS . 'samples';
        mkdir($tmpPath, 0777, true);
        mkdir($basePath, 0777, true);

        return [$tmpPath, $basePath];
    }
}
