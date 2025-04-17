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
