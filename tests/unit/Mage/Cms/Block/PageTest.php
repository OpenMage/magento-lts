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

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Generator;
use Mage_Cms_Block_Page;
use Mage_Cms_Model_Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    /**
     * @dataProvider provideGetPageData
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetPage(string $pageId): void
    {
        $mock = $this->getMockBuilder(Mage_Cms_Block_Page::class)
            ->setMethods(['getPageId'])
            ->getMock();

        $mock->method('getPageId')->willReturn($pageId);
        $this->assertInstanceOf(Mage_Cms_Model_Page::class, $mock->getPage());
    }

    public function provideGetPageData(): Generator
    {
        yield 'valid page ID' => [
            '2'
        ];
        yield 'invalid page ID' => [
            '0'
        ];
    }
}
