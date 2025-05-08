<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Generator;
use Mage;
use Mage_Adminhtml_Helper_Dashboard_Data;
use Mage_Core_Model_Store;
use PHPUnit\Framework\TestCase;

class DefaultConfigTest extends TestCase
{
    /**
     * @dataProvider provideGetStoreConfig
     */
    public function testGetStoreConfig(string $expectedResult, string $path, bool|int|Mage_Core_Model_Store|null|string $store = null): void
    {
        static::assertSame($expectedResult, Mage::getStoreConfig($path, $store));
    }


    public function provideGetStoreConfig(): Generator
    {
        yield Mage_Adminhtml_Helper_Dashboard_Data::XML_PATH_ENABLE_CHARTS => [
            '1',
            Mage_Adminhtml_Helper_Dashboard_Data::XML_PATH_ENABLE_CHARTS,
        ];
    }
}
