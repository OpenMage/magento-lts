<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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
