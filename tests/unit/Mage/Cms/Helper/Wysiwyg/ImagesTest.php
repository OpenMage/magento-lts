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

namespace OpenMage\Tests\Unit\Mage\Cms\Helper\Wysiwyg;

use Mage;
use Mage_Cms_Helper_Wysiwyg_Images as Subject;
use Mage_Cms_Model_Wysiwyg_Images_Storage;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\CmsTrait;
use PHPUnit\Framework\TestCase;

class ImagesTest extends TestCase
{
    use CmsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('cms/wysiwyg_images');
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetCurrentPath(): void
    {
        $this->assertIsString($this->subject->getCurrentPath());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetCurrentUrl(): void
    {
        $this->assertIsString($this->subject->getCurrentUrl());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetStorage(): void
    {
        $this->assertInstanceOf(Mage_Cms_Model_Wysiwyg_Images_Storage::class, $this->subject->getStorage());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testIdEncode(): void
    {
        $this->assertIsString($this->subject->idEncode($this->getTestString()));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testIdDecode(): void
    {
        $this->assertIsString($this->subject->idDecode($this->getTestString()));
    }

    /**
     * @dataProvider provideGetShortFilename
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetShortFilename(string $expectedResult, string $filename, int $maxLength): void
    {
        $this->assertSame($expectedResult, $this->subject->getShortFilename($filename, $maxLength));
    }
}
