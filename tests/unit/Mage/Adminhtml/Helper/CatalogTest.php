<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Mage;
use Mage_Adminhtml_Helper_Catalog as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class CatalogTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/catalog');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Catalog::setAttributeTabBlock()
     * @group Helper
     */
    public function testSetAttributeTabBlock(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->setAttributeTabBlock(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Catalog::setCategoryAttributeTabBlock()
     * @group Helper
     */
    public function testSetCategoryAttributeTabBlock(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->setCategoryAttributeTabBlock(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Catalog::getSitemapValidPaths()
     * @group Helper
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
        static::assertSame($assert, self::$subject->getSitemapValidPaths());
    }
}
