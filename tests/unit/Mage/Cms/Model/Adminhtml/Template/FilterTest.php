<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Adminhtml\Template;

use Iterator;
use Mage;
use Mage_Cms_Model_Adminhtml_Template_Filter as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\Model\Adminhtml\Template\FilterTrait;
use Override;

/**
 * GHSA-7vww-whgh-98cf / GHSA-67pp-qrvw-9j62
 */
final class FilterTest extends OpenMageTest
{
    use FilterTrait;

    private const TEST_DIR = 'ghsa-7vww-test';

    private static Subject $subject;

    private string $mediaDir;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cms/adminhtml_template_filter');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaDir = Mage::getBaseDir('media') . DS . self::TEST_DIR;
        if (!is_dir($this->mediaDir)) {
            mkdir($this->mediaDir, 0o755, true);
        }

        file_put_contents($this->mediaDir . DS . 'image.png', 'not really a png');
    }

    protected function tearDown(): void
    {
        foreach (['image.png', 'outside-link.png'] as $file) {
            if (is_link($this->mediaDir . DS . $file) || is_file($this->mediaDir . DS . $file)) {
                unlink($this->mediaDir . DS . $file);
            }
        }

        if (is_dir($this->mediaDir)) {
            rmdir($this->mediaDir);
        }

        parent::tearDown();
    }

    public function testGetMediaFilePathResolvesFileInsideMedia(): void
    {
        $path = self::$subject->getMediaFilePath('{{media url="' . self::TEST_DIR . '/image.png"}}');

        self::assertSame(realpath($this->mediaDir . DS . 'image.png'), $path);
    }

    /**
     * @dataProvider provideRejectedDirectives
     */
    public function testGetMediaFilePathRejects(string $directive): void
    {
        $this->expectException(Mage_Core_Exception::class);

        self::$subject->getMediaFilePath($directive);
    }

    public function testGetMediaFilePathRejectsSymlinkLeavingMedia(): void
    {
        $link = $this->mediaDir . DS . 'outside-link.png';
        if (!@symlink(Mage::getBaseDir() . DS . 'index.php', $link)) {
            self::markTestSkipped('Symlinks are not supported here.');
        }

        $this->expectException(Mage_Core_Exception::class);

        self::$subject->getMediaFilePath('{{media url="' . self::TEST_DIR . '/outside-link.png"}}');
    }

    public static function provideRejectedDirectives(): Iterator
    {
        yield 'protocol directive (SSRF)' => ['{{protocol url="169.254.169.254/latest/meta-data/"}}'];
        yield 'config directive' => ['{{config path="web/unsecure/base_url"}}'];
        yield 'raw url' => ['http://127.0.0.1/image.png'];
        yield 'raw absolute path' => ['/etc/passwd'];
        yield 'text around media directive' => ['http://127.0.0.1/{{media url="' . self::TEST_DIR . '/image.png"}}'];
        yield 'two directives' => ['{{media url="' . self::TEST_DIR . '/image.png"}}{{media url="x"}}'];
        yield 'traversal out of media' => ['{{media url="../index.php"}}'];
        yield 'deep traversal' => ['{{media url="' . self::TEST_DIR . '/../../app/etc/config.xml"}}'];
        yield 'absolute path in url' => ['{{media url="/etc/passwd"}}'];
        yield 'stream wrapper in url' => ['{{media url="php://filter/resource=index.php"}}'];
        yield 'missing file' => ['{{media url="' . self::TEST_DIR . '/missing.png"}}'];
        yield 'directory' => ['{{media url="' . self::TEST_DIR . '"}}'];
        yield 'no url parameter' => ['{{media}}'];
    }
}
