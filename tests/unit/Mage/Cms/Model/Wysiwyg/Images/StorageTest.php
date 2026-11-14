<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg\Images;

use Override;
use Mage;
use Mage_Adminhtml_Model_Session;
use Mage_Cms_Helper_Wysiwyg_Images;
use Mage_Cms_Model_Wysiwyg_Images_Storage as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Object;

final class StorageTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cms/wysiwyg_images_storage');
    }

    /**
     * @group Model
     */
    public function testGetThumbsPath(): void
    {
        self::assertIsString(self::$subject->getThumbsPath());
    }

    /**
     * @group Model
     */
    public function testResizeOnTheFly(): void
    {
        self::assertFalse(self::$subject->resizeOnTheFly('not-existing.jpeg'));
    }

    /**
     * GHSA-w949-chmj-mx9m: a filename that climbs out of the storage root never reaches resizeFile()
     *
     * @group Model
     */
    public function testResizeOnTheFlyRefusesPathOutsideStorageRoot(): void
    {
        $helper = Mage::helper('cms/wysiwyg_images');
        $current = $helper->getCurrentPath();
        self::assertIsString($current);
        $currentReal = realpath($current);
        self::assertIsString($currentReal);

        $outside = tempnam(sys_get_temp_dir(), 'w949');
        self::assertIsString($outside);
        $inside = $current . DS . 'w949-' . uniqid() . '.png';
        touch($inside);

        try {
            $climb = str_repeat('..' . DS, substr_count(trim($currentReal, DS), DS) + 1);

            $subject = $this->createPartialMock(Subject::class, ['resizeFile', 'getSession']);
            $subject->method('getSession')->willReturn(new Varien_Object());
            $subject->expects(self::once())
                ->method('resizeFile')
                ->with($inside)
                ->willReturn('resized');

            self::assertFalse($subject->resizeOnTheFly($climb . ltrim($outside, DS)));
            self::assertSame('resized', $subject->resizeOnTheFly(basename($inside)));
        } finally {
            @unlink($outside);
            @unlink($inside);
        }
    }

    /**
     * @group Model
     */
    public function testGetHelper(): void
    {
        self::assertInstanceOf(Mage_Cms_Helper_Wysiwyg_Images::class, self::$subject->getHelper());
    }

    /**
     * @group Model
     */
    public function testGetSession(): void
    {
        self::assertInstanceOf(Mage_Adminhtml_Model_Session::class, self::$subject->getSession());
    }

    /**
     * @group Model
     */
    public function testGetThumbnailRoot(): void
    {
        self::assertIsString(self::$subject->getThumbnailRoot());
    }

    /**
     * @group Model
     */
    public function testIsImage(): void
    {
        self::assertIsBool(self::$subject->isImage('test.jpeg'));
    }
}
