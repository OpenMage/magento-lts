<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideGetStoreConfig
 * @group Base
 * @group Default_Config
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Generator;
use Mage;
use Mage_Adminhtml_Block_Dashboard;
use PHPUnit\Framework\TestCase;

class DefaultConfigTest extends TestCase
{
    public function testGetStoreConfig(string $expectedResult, string $path, $store = null): void
    {
        $this->assertSame($expectedResult, Mage::getStoreConfig($path, $store));
    }


    public function provideGetStoreConfig(): Generator
    {
        yield Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS => [
            '1',
            Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS,
        ];
    }
}
