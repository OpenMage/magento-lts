<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg\Images;

use Mage;
use Mage_Adminhtml_Model_Session;
use Mage_Cms_Helper_Wysiwyg_Images;
use Mage_Cms_Model_Wysiwyg_Images_Storage as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class StorageTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cms/wysiwyg_images_storage');
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetThumbsPath(): void
    {
        static::assertIsString(self::$subject->getThumbsPath());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testResizeOnTheFly(): void
    {
        static::assertFalse(self::$subject->resizeOnTheFly('not-existing.jpeg'));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetHelper(): void
    {
        static::assertInstanceOf(Mage_Cms_Helper_Wysiwyg_Images::class, self::$subject->getHelper());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetSession(): void
    {
        static::assertInstanceOf(Mage_Adminhtml_Model_Session::class, self::$subject->getSession());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetThumbnailRoot(): void
    {
        static::assertIsString(self::$subject->getThumbnailRoot());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsImage(): void
    {
        static::assertIsBool(self::$subject->isImage('test.jpeg'));
    }
}
