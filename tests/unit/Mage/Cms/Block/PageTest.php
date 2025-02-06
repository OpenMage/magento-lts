<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Mage_Cms_Block_Page as Subject;
use Mage_Cms_Model_Page;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    use NumericStringTrait;

    /**
     * @dataProvider provideNumericString
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetPage(string $pageId): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getPageId'])
            ->getMock();

        $mock->method('getPageId')->willReturn($pageId);
        $this->assertInstanceOf(Mage_Cms_Model_Page::class, $mock->getPage());
    }
}
