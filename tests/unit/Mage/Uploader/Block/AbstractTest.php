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

namespace OpenMage\Tests\Unit\Mage\Uploader\Block;

use Mage;
use Mage_Uploader_Block_Abstract as Subject;
use Mage_Uploader_Model_Config_Browsebutton;
use Mage_Uploader_Model_Config_Misc;
use Mage_Uploader_Model_Config_Uploader;
use OpenMage\Tests\Unit\OpenMageTest;

class AbstractTest extends OpenMageTest
{
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Block
     */
    public function testGetMiscConfig(): void
    {
        static::assertInstanceOf(Mage_Uploader_Model_Config_Misc::class, self::$subject->getMiscConfig());
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetUploaderConfig(): void
    {
        static::assertInstanceOf(Mage_Uploader_Model_Config_Uploader::class, self::$subject->getUploaderConfig());
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Block
     */
    public function testGetButtonConfig(): void
    {
        static::assertInstanceOf(Mage_Uploader_Model_Config_Browsebutton::class, self::$subject->getButtonConfig());
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Block
     */
    public function testGetElementId(): void
    {
        $suffix = 'test';
        $result = self::$subject->getElementId($suffix);
        static::assertStringStartsWith('id_', $result);
        static::assertStringEndsWith('-' . $suffix, $result);
    }
}
