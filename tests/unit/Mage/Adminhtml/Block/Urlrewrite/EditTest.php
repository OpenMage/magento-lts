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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Urlrewrite;

use Mage;
use Mage_Adminhtml_Block_Urlrewrite_Edit;
use Mage_Catalog_Model_Category;
use Mage_Catalog_Model_Product;
use Mage_Core_Model_Layout;
use Mage_Core_Model_Url_Rewrite;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Urlrewrite_Edit $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();

        if (!Mage::registry('current_product')) {
            Mage::register('current_product', new Mage_Catalog_Model_Product());
        }

        if (!Mage::registry('current_category')) {
            Mage::register('current_category', new Mage_Catalog_Model_Category());
        }

        Mage::register('current_urlrewrite', new Mage_Core_Model_Url_Rewrite());

        self::$subject = new Mage_Adminhtml_Block_Urlrewrite_Edit();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Urlrewrite_Edit::getButtonSkipCategoriesBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonBackBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSkipCategoriesBlock();
        $this->assertSame('Skip Category Selection', $result->getLabel());
        $this->assertStringStartsWith('window.location = \'', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
        $this->assertSame(-1, $result->getLevel());
    }
}
