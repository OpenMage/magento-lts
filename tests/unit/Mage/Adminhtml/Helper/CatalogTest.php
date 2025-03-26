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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Mage;
use Mage_Adminhtml_Helper_Catalog as Subject;
use PHPUnit\Framework\TestCase;

class CatalogTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/catalog');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Catalog::setAttributeTabBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testSetAttributeTabBlock(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setAttributeTabBlock(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Catalog::setCategoryAttributeTabBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testSetCategoryAttributeTabBlock(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setCategoryAttributeTabBlock(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Catalog::getSitemapValidPaths()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetSitemapValidPaths(): void
    {
        $assert = [
            'available' => [
                'any_path'  => '/*/*.xml',
            ],
            'protected' => [
                'app'       => '/app/*/*',
                'dev'       => '/dev/*/*',
                'errors'    => '/errors/*/*',
                'js'        => '/js/*/*',
                'lib'       => '/lib/*/*',
                'shell'     => '/shell/*/*',
                'skin'      => '/skin/*/*',
            ],
        ];
        $this->assertSame($assert, $this->subject->getSitemapValidPaths());
    }
}
