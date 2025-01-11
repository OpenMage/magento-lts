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

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Generator;
use Mage;
use Mage_Cms_Helper_Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public Mage_Cms_Helper_Page $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('cms/page');
    }

    /**
     * @dataProvider provideGetUsedInStoreConfigPaths
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetUsedInStoreConfigPaths(array $expectedResult, ?array $path): void
    {
        $this->assertSame($expectedResult, Mage_Cms_Helper_Page::getUsedInStoreConfigPaths($path));
    }

    public function provideGetUsedInStoreConfigPaths(): Generator
    {
        yield 'null' => [
            [],
            null,
        ];
        yield 'empty array' => [
            [
                0 => Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE,
                1 => Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE,
                2 => Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE,
            ],
            [],
        ];
        yield 'custom paths' => [
            [
                0 => Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE,
                1 => Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE,
                2 => Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE,
                3 => 'my/first/path',
                4 => 'my/second/path',
            ],
            [
                'my/first/path',
                'my/second/path',
            ],
        ];
    }
}
