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

namespace OpenMage\Tests\Unit\Mage\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mage;
use Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class SamplesTest extends TestCase
{
    private static ?Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples::getButtonUploadBlock()
     * @group Mage_Downloadable
     * @group Mage_Downloadable_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
//    public function testGetButtonUploadBlock(): void
//    {
//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonUploadBlock();
//        $this->assertSame('', $result->getId());
//        $this->assertSame('Upload Files', $result->getLabel());
//        $this->assertSame('Downloadable.massUploadByType(\'samples\')', $result->getOnClick());
//        $this->assertNull($result->getClass());
//        $this->assertSame('button', $result->getType());
//    }
}
