<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Archive;

use FilesystemIterator;
use Generator;
use Mage_Archive_Tar as Subject;
use Mage_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class TarTest extends OpenMageTest
{
    private string $root;

    private string $destination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = sys_get_temp_dir() . DS . uniqid('om-tar-', true);
        $this->destination = $this->root . DS . 'extract' . DS;
        mkdir($this->destination, 0777, true);
    }

    protected function tearDown(): void
    {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($items as $item) {
            $item->isDir() && !$item->isLink() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($this->root);
        parent::tearDown();
    }

    /**
     * @group Mage_Archive
     */
    public function testUnpackExtractsContainedEntries(): void
    {
        $this->unpack(
            $this->entry('sub/', '5', mode: 0755),
            $this->entry('sub/file.txt', '0', 'contents'),
            $this->entry('link', '2', link: 'sub/file.txt'),
        );

        self::assertSame('contents', file_get_contents($this->destination . 'sub/file.txt'));
        self::assertTrue(is_link($this->destination . 'link'));
        self::assertSame('sub/file.txt', readlink($this->destination . 'link'));
    }

    /**
     * @dataProvider provideEscapingNames
     * @group Mage_Archive
     */
    public function testUnpackRejectsEntryOutsideDestination(string $name): void
    {
        $this->expectException(Mage_Exception::class);
        $this->expectExceptionMessage('escapes the destination directory');

        try {
            $this->unpack($this->entry($name, '0', 'payload'));
        } finally {
            self::assertFileDoesNotExist($this->root . DS . 'escaped.txt');
        }
    }

    public static function provideEscapingNames(): Generator
    {
        yield 'parent segment' => ['../escaped.txt'];
        yield 'nested parent segments' => ['sub/../../escaped.txt'];
        yield 'backslash parent segment' => ['..\\escaped.txt'];
        yield 'absolute path' => ['/om-tar-test-nonexistent/escaped.txt'];
    }

    /**
     * A link pointing outside the destination, followed by a regular file of the same name,
     * must not write through the link.
     *
     * @dataProvider provideEscapingLinkTargets
     * @group Mage_Archive
     */
    public function testUnpackSkipsLinkOutsideDestination(string $target): void
    {
        $target = str_replace('{root}', $this->root, $target);

        $this->unpack(
            $this->entry('evil.txt', '2', link: $target),
            $this->entry('evil.txt', '0', 'payload'),
        );

        self::assertFileDoesNotExist($this->root . DS . 'escaped.txt');
        self::assertFalse(is_link($this->destination . 'evil.txt'));
        self::assertSame('payload', file_get_contents($this->destination . 'evil.txt'));
    }

    public static function provideEscapingLinkTargets(): Generator
    {
        yield 'relative' => ['../escaped.txt'];
        yield 'absolute' => ['{root}/escaped.txt'];
    }

    /**
     * @group Mage_Archive
     */
    public function testUnpackDropsSpecialModeBits(): void
    {
        $this->unpack($this->entry('setuid.sh', '0', 'payload', 04755));

        self::assertSame(0, fileperms($this->destination . 'setuid.sh') & 07000);
    }

    private function unpack(string ...$entries): void
    {
        $archive = $this->root . DS . 'archive.tar';
        file_put_contents($archive, implode('', $entries) . str_repeat("\0", 1024));
        (new Subject())->unpack($archive, $this->destination);
    }

    /**
     * Build one ustar entry: a 512-byte header followed by the data padded to the block size
     */
    private function entry(string $name, string $type, string $data = '', int $mode = 0644, string $link = ''): string
    {
        $header = str_pad($name, 100, "\0")
            . sprintf('%07o', $mode) . "\0"
            . "0000000\0" . "0000000\0"
            . sprintf('%011o', strlen($data)) . "\0"
            . sprintf('%011o', 0) . "\0"
            . str_repeat(' ', 8)
            . $type
            . str_pad($link, 100, "\0")
            . "ustar\0" . '00'
            . str_repeat("\0", 247);
        $checksum = array_sum(array_map(ord(...), str_split($header)));
        $header = substr_replace($header, sprintf('%06o', $checksum) . "\0 ", 148, 8);

        $padding = (Subject::TAR_BLOCK_SIZE - strlen($data) % Subject::TAR_BLOCK_SIZE) % Subject::TAR_BLOCK_SIZE;

        return $header . $data . str_repeat("\0", $padding);
    }
}
