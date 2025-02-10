<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Cms
 * @group Mage_Cms_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg\Images;

use Mage;
use Mage_Adminhtml_Model_Session;
use Mage_Cms_Helper_Wysiwyg_Images;
use Mage_Cms_Model_Wysiwyg_Images_Storage as Subject;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('cms/wysiwyg_images_storage');
    }

    
    public function testGetThumbsPath(): void
    {
        $this->assertIsString($this->subject->getThumbsPath());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testResizeOnTheFly(): void
    {
        $this->assertFalse($this->subject->resizeOnTheFly('not-existing.jpeg'));
    }

    
    public function testGetHelper(): void
    {
        $this->assertInstanceOf(Mage_Cms_Helper_Wysiwyg_Images::class, $this->subject->getHelper());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetSession(): void
    {
        $this->assertInstanceOf(Mage_Adminhtml_Model_Session::class, $this->subject->getSession());
    }

    
    public function testGetThumbnailRoot(): void
    {
        $this->assertIsString($this->subject->getThumbnailRoot());
    }

    
    public function testIsImage(): void
    {
        $this->assertIsBool($this->subject->isImage('test.jpeg'));
    }
}
