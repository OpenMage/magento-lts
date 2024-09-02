<?php

declare(strict_types=1);

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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Exception;
use Mage_Core_Helper_EnvironmentConfigLoader;
use PHPUnit\Framework\TestCase;
use Varien_Simplexml_Config;

class EnvironmentConfigLoaderTest extends TestCase
{
    public const XML_PATH_GENERAL = 'general/store_information/name';

    public const XML_PATH_DEFAULT = 'default/general/store_information/name';

    public const XML_PATH_WEBSITE = 'websites/base/general/store_information/name';

    public const XML_PATH_STORE = 'stores/german/general/store_information/name';

    /**
     * @throws Mage_Core_Exception
     */
    public function setup(): void
    {
        Mage::setRoot();
    }

    /**
     * @group Mage_Core
     */
    public function testBuildPath(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        $path = $environmentConfigLoaderHelper->exposedBuildPath('GENERAL', 'STORE_INFORMATION', 'NAME');
        $this->assertEquals(self::XML_PATH_GENERAL, $path);
    }

    /**
     * @group Mage_Core
     */
    public function testBuildNodePath(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        $nodePath = $environmentConfigLoaderHelper->exposedBuildNodePath('DEFAULT', self::XML_PATH_GENERAL);
        $this->assertEquals(self::XML_PATH_DEFAULT, $nodePath);
    }

    /**
     * @group Mage_Core
     */
    public function testXmlHasTestStrings(): void
    {
        $xmlStruct = $this->getTestXml();
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);
        $this->assertEquals('test_default', (string)$xml->getNode(self::XML_PATH_DEFAULT));
        $this->assertEquals('test_website', (string)$xml->getNode(self::XML_PATH_WEBSITE));
        $this->assertEquals('test_store', (string)$xml->getNode(self::XML_PATH_STORE));
    }

    /**
     * @dataProvider envOverridesCorrectConfigKeysDataProvider
     * @param array $config
     *
     * @group Mage_Core
     */
    public function testEnvOverridesForValidConfigKeys(array $config): void
    {
        $xmlStruct = $this->getTestXml();

        $xmlDefault = new Varien_Simplexml_Config();
        $xmlDefault->loadString($xmlStruct);
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);

        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        $loader->setEnvStore([
            $config['env_path'] => $config['value']
        ]);
        $loader->overrideEnvironment($xml);

        $configPath = $config['xml_path'];
        $defaultValue = $xmlDefault->getNode($configPath);
        $valueAfterOverride = $xml->getNode($configPath);

        // assert
        $this->assertNotEquals((string)$defaultValue, (string)$valueAfterOverride, 'Default value was not overridden.');
    }

    /**
     * @return array<array<string, array<string, string>>>
     */
    public function envOverridesCorrectConfigKeysDataProvider(): array
    {
        $defaultPath = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME';
        $defaultPathWithDash = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__FOO-BAR__NAME';
        $defaultPathWithUnderscore = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__FOO_BAR__NAME';

        $websitePath = 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__STORE_INFORMATION__NAME';
        $websiteWithDashPath = 'OPENMAGE_CONFIG__WEBSITES__BASE-AT__GENERAL__STORE_INFORMATION__NAME';
        $websiteWithUnderscorePath = 'OPENMAGE_CONFIG__WEBSITES__BASE_CH__GENERAL__STORE_INFORMATION__NAME';

        $storeWithDashPath = 'OPENMAGE_CONFIG__STORES__GERMAN-AT__GENERAL__STORE_INFORMATION__NAME';
        $storeWithUnderscorePath = 'OPENMAGE_CONFIG__STORES__GERMAN_CH__GENERAL__STORE_INFORMATION__NAME';
        $storePath = 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME';

        return [
            [
                'Case DEFAULT overrides.' => [
                    'case'     => 'DEFAULT',
                    'xml_path' => self::XML_PATH_DEFAULT,
                    'env_path' => $defaultPath,
                    'value'    => 'default_new_value'
                ]
            ],
            [
                'Case DEFAULT overrides.' => [
                    'case'     => 'DEFAULT',
                    'xml_path' => 'default/general/foo-bar/name',
                    'env_path' => $defaultPathWithDash,
                    'value'    => 'baz'
                ]
            ],
            [
                'Case DEFAULT overrides.' => [
                    'case'     => 'DEFAULT',
                    'xml_path' => 'default/general/foo_bar/name',
                    'env_path' => $defaultPathWithUnderscore,
                    'value'    => 'baz'
                ]
            ],
            [
                'Case STORE overrides.' => [
                    'case'     => 'STORE',
                    'xml_path' => self::XML_PATH_STORE,
                    'env_path' => $storePath,
                    'value'    => 'store_new_value'
                ]
            ],
            [
                'Case STORE overrides.' => [
                    'case'     => 'STORE',
                    'xml_path' => 'stores/german-at/general/store_information/name',
                    'env_path' => $storeWithDashPath,
                    'value'    => 'store_new_value'
                ]
            ],
            [
                'Case STORE overrides.' => [
                    'case'     => 'STORE',
                    'xml_path' => 'stores/german_ch/general/store_information/name',
                    'env_path' => $storeWithUnderscorePath,
                    'value'    => 'store_new_value'
                ]
            ],
            [
                'Case WEBSITE overrides.' => [
                    'case'     => 'WEBSITE',
                    'xml_path' => self::XML_PATH_WEBSITE,
                    'env_path' => $websitePath,
                    'value'    => 'website_new_value'
                ]
            ],
            [
                'Case WEBSITE overrides.' => [
                    'case'     => 'WEBSITE',
                    'xml_path' => 'websites/base_ch/general/store_information/name',
                    'env_path' => $websiteWithUnderscorePath,
                    'value'    => 'website_new_value'
                ]
            ],
            [
                'Case WEBSITE overrides.' => [
                    'case'     => 'WEBSITE',
                    'xml_path' => 'websites/base-at/general/store_information/name',
                    'env_path' => $websiteWithDashPath,
                    'value'    => 'website_new_value'
                ]
            ]
        ];
    }

    /**
     * @dataProvider envDoesNotOverrideOnWrongConfigKeysDataProvider
     * @param array<string, string> $config
     *
     * @group Mage_Core
     */
    public function testEnvDoesNotOverrideForInvalidConfigKeys(array $config): void
    {
        $xmlStruct = $this->getTestXml();

        $xmlDefault = new Varien_Simplexml_Config();
        $xmlDefault->loadString($xmlStruct);
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);

        $defaultValue = 'test_default';
        $this->assertEquals($defaultValue, (string)$xml->getNode(self::XML_PATH_DEFAULT));
        $defaultWebsiteValue = 'test_website';
        $this->assertEquals($defaultWebsiteValue, (string)$xml->getNode(self::XML_PATH_WEBSITE));
        $defaultStoreValue = 'test_store';
        $this->assertEquals($defaultStoreValue, (string)$xml->getNode(self::XML_PATH_STORE));

        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        $loader->setEnvStore([
            $config['path'] => $config['value']
        ]);
        $loader->overrideEnvironment($xml);

        $valueAfterCheck = '';
        switch ($config['case']) {
            case 'DEFAULT':
                $valueAfterCheck = $xml->getNode(self::XML_PATH_DEFAULT);
                break;
            case 'STORE':
                $valueAfterCheck = $xml->getNode(self::XML_PATH_STORE);
                break;
            case 'WEBSITE':
                $valueAfterCheck = $xml->getNode(self::XML_PATH_WEBSITE);
                break;
        }

        // assert
        $this->assertTrue(!str_contains('value_will_not_be_changed', (string)$valueAfterCheck), 'Default value was wrongfully overridden.');
    }

    /**
     * @return array<array<string, array<string, string>>>
     */
    public function envDoesNotOverrideOnWrongConfigKeysDataProvider(): array
    {
        $defaultPath = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__ST';
        $websitePath = 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__ST';
        $storePath = 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__ST';

        return [
            [
                'Case DEFAULT with ' . $defaultPath . ' will not override.' => [
                    'case'  => 'DEFAULT',
                    'path'  => $defaultPath,
                    'value' => 'default_value_will_not_be_changed'
                ]
            ],
            [
                'Case STORE with ' . $storePath . ' will not override.' => [
                    'case'  => 'STORE',
                    'path'  => $storePath,
                    'value' => 'store_value_will_not_be_changed'
                ]
            ],
            [
                'Case WEBSITE with ' . $websitePath . ' will not override.' => [
                    'case'  => 'WEBSITE',
                    'path'  => $websitePath,
                    'value' => 'website_value_will_not_be_changed'
                ]
            ]
        ];
    }

    /**
     * @return string
     */
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
                    <name>test_default</name>
            </foo-bar>
            <foo_bar>
                    <name>test_default</name>
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
