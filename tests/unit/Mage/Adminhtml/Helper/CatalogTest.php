<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Adminhtml_Helper_Catalog::setAttributeTabBlock()
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Helper
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
