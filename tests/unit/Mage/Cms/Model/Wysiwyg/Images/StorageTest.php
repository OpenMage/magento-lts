<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg\Images;

use Mage;
use Mage_Adminhtml_Model_Session;
use Mage_Cms_Helper_Wysiwyg_Images;
use Mage_Cms_Model_Wysiwyg_Images_Storage as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class StorageTest extends OpenMageTest
{
    private static Subject $subject;

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
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testResizeOnTheFly(): void
    {
        self::assertFalse(self::$subject->resizeOnTheFly('not-existing.jpeg'));
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
     * @group runInSeparateProcess
     * @runInSeparateProcess
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
