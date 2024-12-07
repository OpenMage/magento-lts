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
use Mage_Core_Model_Layout;
use Mage_Uploader_Block_Multiple;
use PHPUnit\Framework\TestCase;

class MultipleTest extends TestCase
{
    private static ?Mage_Uploader_Block_Multiple $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Uploader_Block_Multiple();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonUploadBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonUploadBlock();
        $this->assertStringStartsWith('id_', $result->getId());
        $this->assertSame('Upload Files', $result->getLabel());
        $this->assertNull($result->getOnClick());
        $this->assertSame('button', $result->getType());
        $this->assertNull($result->getClass());
    }
}
