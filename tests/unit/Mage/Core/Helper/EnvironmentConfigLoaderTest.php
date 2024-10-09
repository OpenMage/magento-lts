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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Generator;
use Mage;
use Mage_Core_Helper_EnvironmentConfigLoader;
use PHPUnit\Framework\TestCase;
use Varien_Simplexml_Config;

class EnvironmentConfigLoaderTest extends TestCase
{
    public const XML_PATH_GENERAL = 'general/store_information/name';

    public const XML_PATH_DEFAULT = 'default/general/store_information/name';

    public const XML_PATH_WEBSITE = 'websites/base/general/store_information/name';

    public const XML_PATH_STORE = 'stores/german/general/store_information/name';

    public Mage_Core_Helper_EnvironmentConfigLoader $subject;

    public function setup(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/environmentConfigLoader');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testBuildPath(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        $path = $environmentConfigLoaderHelper->exposedBuildPath('GENERAL', 'STORE_INFORMATION', 'NAME');
        $this->assertSame(self::XML_PATH_GENERAL, $path);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testBuildNodePath(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        $nodePath = $environmentConfigLoaderHelper->exposedBuildNodePath('DEFAULT', self::XML_PATH_GENERAL);
        $this->assertSame(self::XML_PATH_DEFAULT, $nodePath);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     * @group EnvLoader
     */
    public function testXmlHasTestStrings(): void
    {
        $xmlStruct = $this->getTestXml();
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);

        $this->assertSame('test_default', (string)$xml->getNode(self::XML_PATH_DEFAULT));
        $this->assertSame('test_website', (string)$xml->getNode(self::XML_PATH_WEBSITE));
        $this->assertSame('test_store', (string)$xml->getNode(self::XML_PATH_STORE));
    }

    /**
     * @dataProvider provideOverrideEnvironment
     * @group Mage_Core
     * @group Mage_Core_Helper
     *
     * @param array<string, string> $params
     */
    public function testOverrideEnvironmentNode(string $expectedResult, array $params): void
    {
        $config = Mage::getConfig();
        $this->subject->overrideEnvironment($config);
        $this->assertSame($expectedResult, trim((string)$config->getNode($params['xmlPath'])));
    }

    /**
     * @dataProvider provideOverrideEnvironment
     * @group Mage_Core
     * @group Mage_Core_Helper
     *
     * @param array<string, string> $params
     */
    public function testOverrideEnvironmentConfig(string $expectedResult, array $params): void
    {
        $config = Mage::getConfig();
        $this->subject->overrideEnvironment($config);

        $configPath = explode('/', $params['xmlPath']);
        unset($configPath[0], $configPath[1]);
        $configPath = implode('/', $configPath);

        $this->assertSame($expectedResult, (string)Mage::getStoreConfig($configPath, $params['storeId']));
    }

    public function provideOverrideEnvironment(): Generator
    {
        yield 'Case DEFAULT overrides' => [
            'ENV default',
            [
                'xmlPath'   => self::XML_PATH_DEFAULT,
                'envPath'   => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case DEFAULT overrides w/ dashes' => [
            'ENV default dashes',
            [
                'xmlPath'   => 'stores/default/general/foo-bar/name',
                'envPath'   => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__FOO-BAR__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case DEFAULT overrides w/ underscore' => [
            'ENV default underscore',
            [
                'xmlPath'   => 'stores/default/general/foo_bar/name',
                'envPath'   => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__FOO_BAR__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case DEFAULT will not override' => [
            '',
            [
                'xmlPath'   => '',
                'envPath'   => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__ST',
                'storeId'   => null
            ]
        ];

        yield 'Case WEBSITE overrides' => [
            'ENV website',
            [
                'xmlPath'   => self::XML_PATH_WEBSITE,
                'envPath'   => 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case WEBSITE overrides w/ dashes' => [
            'ENV website dashes',
            [
                'xmlPath'   => 'websites/base-at/general/store_information/name',
                'envPath'   => 'OPENMAGE_CONFIG__WEBSITES__BASE-AT__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case WEBSITE overrides w/ underscore' => [
            'ENV website underscore',
            [
                'xmlPath'   => 'websites/base_ch/general/store_information/name',
                'envPath'   => 'OPENMAGE_CONFIG__WEBSITES__BASE_CH__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case WEBSITE will not override' => [
            '',
            [
                'xmlPath'   => '',
                'envPath'  => 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__ST',
                'storeId'   => null
            ]
        ];

        yield 'Case STORE overrides' => [
            'ENV store',
            [
                'xmlPath'   => self::XML_PATH_STORE,
                'envPath'   => 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case STORE overrides w/ dashes' => [
            'ENV store dashes',
            [
                'xmlPath'   => 'stores/german-at/general/store_information/name',
                'envPath'   => 'OPENMAGE_CONFIG__STORES__GERMAN-AT__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case STORE overrides w/ underscore' => [
            'ENV store underscore',
            [
                'xmlPath'   => 'stores/german_ch/general/store_information/name',
                'envPath'   => 'OPENMAGE_CONFIG__STORES__GERMAN_CH__GENERAL__STORE_INFORMATION__NAME',
                'storeId'   => null
            ]
        ];
        yield 'Case STORE will not override' => [
            '',
            [
                'xmlPath'   => '',
                'envPath'   => 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__ST',
                'storeId'   => null
            ]
        ];
    }

    public function getTestXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<config>
    <default>
        <general>
            <store_information>
                    <name>test_default</name>
            </store_information>
            <foo-bar>
                    <name>test_dashes</name>
            </foo-bar>
            <foo_bar>
                    <name>test_underscore</name>
            </foo_bar>
        </general>
    </default>
    
    <websites>
        <base>
            <general>
                <store_information>
                        <name>test_website</name>
                </store_information>
            </general>
        </base>
        <base-at>
            <general>
                <store_information>
                        <name>test_website</name>
                </store_information>
            </general>
        </base-at>
        <base_ch>
            <general>
                <store_information>
                        <name>test_website</name>
                </store_information>
            </general>
        </base_ch>
    </websites>
    <stores>
        <german>
            <general>
                <store_information>
                        <name>test_store</name>
                </store_information>
            </general>
        </german>
        <german-at>
            <general>
                <store_information>
                        <name>test_store</name>
                </store_information>
            </general>
        </german-at>
        <german_ch>
            <general>
                <store_information>
                        <name>test_store</name>
                </store_information>
            </general>
        </german_ch>
    </stores>
</config>
XML;
    }
}
