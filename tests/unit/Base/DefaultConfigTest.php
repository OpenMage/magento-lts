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

namespace OpenMage\Tests\Unit\Base;

use Generator;
use Mage;
use Mage_Adminhtml_Block_Dashboard;
use PHPUnit\Framework\TestCase;

class DefaultConfigTest extends TestCase
{
    /**
     * @dataProvider provideGetStoreConfig
     * @group Base
     * @group Default_Config
     */
    public function testGetStoreConfig(string $expectedResult, string $path, $store = null): void
    {
        $this->assertSame($expectedResult, Mage::getStoreConfig($path, $store));
    }


    public function provideGetStoreConfig(): Generator
    {
        yield Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS => [
            '1',
            Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS
        ];
    }
}
