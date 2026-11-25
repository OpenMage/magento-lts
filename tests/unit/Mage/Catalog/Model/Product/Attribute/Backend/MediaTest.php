<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product\Attribute\Backend;

use Mage;
use Mage_Catalog_Model_Product_Attribute_Backend_Media as Subject;
use Mage_Catalog_Model_Product_Media_Config;
use Mage_Core_Exception;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\Attribute\Backend\MediaTrait;
use ReflectionMethod;
use Varien_Io_File;

final class MediaTest extends OpenMageTest
{
    use MediaTrait;

    private const CONFIG_SINGLETON = '_singleton/catalog/product_media_config';

    private static Subject $subject;

    private string $root = '';

    private string $site = '';

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/product_attribute_backend_media');
    }

    /**
     * Point the product media config at a throwaway directory so that a regression cannot move
     * files of the checkout. Layout: <root>/site/media/{catalog,tmp/catalog}/product.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->root = sys_get_temp_dir() . DS . 'openmage-media-' . bin2hex(random_bytes(6));
        $this->site = $this->root . DS . 'site';
        $media = $this->site . DS . 'media';
        mkdir($media . DS . 'catalog' . DS . 'product', 0777, true);
        mkdir($media . DS . 'tmp' . DS . 'catalog' . DS . 'product', 0777, true);
        file_put_contents($this->site . DS . 'protected.txt', 'secret');
        $config = new class extends Mage_Catalog_Model_Product_Media_Config {
            public string $media = '';

            #[Override]
            public function getBaseMediaPath()
            {
                return $this->media . DS . 'catalog' . DS . 'product';
            }

            #[Override]
            public function getBaseTmpMediaPath()
            {
                return $this->media . DS . 'tmp' . DS . 'catalog' . DS . 'product';
            }
        };
        $config->media = $media;
        Mage::unregister(self::CONFIG_SINGLETON);
        Mage::register(self::CONFIG_SINGLETON, $config);
    }

    protected function tearDown(): void
    {
        Mage::unregister(self::CONFIG_SINGLETON);
        (new Varien_Io_File())->rmdir($this->root, true);
        parent::tearDown();
    }

    /**
     * @group Model
     */
    public function testMoveImageFromTmpMovesUploadedImage(): void
    {
        $tmpDir = $this->site . DS . 'media' . DS . 'tmp' . DS . 'catalog' . DS . 'product' . DS . 'a' . DS . 'b';
        mkdir($tmpDir, 0777, true);
        file_put_contents($tmpDir . DS . 'image.jpg', 'jpg');

        $result = (new ReflectionMethod(self::$subject, '_moveImageFromTmp'))->invoke(self::$subject, '/a/b/image.jpg.tmp');

        self::assertSame('/a/b/image.jpg', $result);
        self::assertFileExists($this->site . DS . 'media' . DS . 'catalog' . DS . 'product' . DS . 'a' . DS . 'b' . DS . 'image.jpg');
        self::assertFileDoesNotExist($tmpDir . DS . 'image.jpg');
    }

    /**
     * Gallery entries without a value_id are moved out of media/tmp/catalog/product; the file
     * name comes from the product save request and must not leave the media directories.
     *
     * @dataProvider provideTraversalFileData
     *
     * @group Model
     */
    public function testMoveImageFromTmpRejectsTraversal(string $file): void
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Detected malicious path or filename input.');

        try {
            (new ReflectionMethod(self::$subject, '_moveImageFromTmp'))->invoke(self::$subject, $file);
        } finally {
            self::assertFileExists($this->site . DS . 'protected.txt');
            self::assertFileDoesNotExist($this->root . DS . 'protected.txt');
        }
    }

    /**
     * @dataProvider provideTraversalFileData
     *
     * @group Model
     */
    public function testCopyImageRejectsTraversal(string $file): void
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Detected malicious path or filename input.');

        (new ReflectionMethod(self::$subject, '_copyImage'))->invoke(self::$subject, $file);
    }

    /**
     * @dataProvider provideValidFileData
     *
     * @group Model
     */
    public function testValidateImageFileAcceptsGalleryNames(string $file): void
    {
        self::assertNull((new ReflectionMethod(self::$subject, '_validateImageFile'))->invoke(self::$subject, $file));
    }
}
