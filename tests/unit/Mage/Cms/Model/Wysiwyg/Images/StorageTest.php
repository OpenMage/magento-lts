<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
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

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
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

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetThumbnailRoot(): void
    {
        $this->assertIsString($this->subject->getThumbnailRoot());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsImage(): void
    {
        $this->assertIsBool($this->subject->isImage('test.jpeg'));
    }
}
