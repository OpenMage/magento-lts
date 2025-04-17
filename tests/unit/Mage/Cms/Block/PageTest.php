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
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Mage_Cms_Block_Page as Subject;
use Mage_Cms_Model_Page;
use Mage_Core_Model_Store_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;

class PageTest extends OpenMageTest
{
    use NumericStringTrait;

    /**
     * @dataProvider provideNumericString
     * @group Block
     * @throws Mage_Core_Model_Store_Exception
     */
    public function testGetPage(string $pageId): void
    {
        $methods = [
            'getPageId' => $pageId,
        ];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertInstanceOf(Mage_Cms_Model_Page::class, $mock->getPage());
    }
}
