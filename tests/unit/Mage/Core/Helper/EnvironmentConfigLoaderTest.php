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
     */
    public function testXmlHasTestStrings(): void
    {
        $xmlStruct = $this->getTestXml();
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);
        $this->assertSame('test_default', (string) $xml->getNode(self::XML_PATH_DEFAULT));
        $this->assertSame('test_website', (string) $xml->getNode(self::XML_PATH_WEBSITE));
        $this->assertSame('test_store', (string) $xml->getNode(self::XML_PATH_STORE));
    }

    /**
     * @dataProvider envOverridesCorrectConfigKeysDataProvider
     * @group Mage_Core
     * @group Mage_Core_Helper
     *
     * @param array<string, string> $config
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
            $config['env_path'] => $config['value'],
        ]);
        $loader->overrideEnvironment($xml);

        $configPath = $config['xml_path'];
        $defaultValue = $xmlDefault->getNode($configPath);
        $valueAfterOverride = $xml->getNode($configPath);

        // assert
        $this->assertNotSame((string) $defaultValue, (string) $valueAfterOverride, 'Default value was not overridden.');
    }

    public function envOverridesCorrectConfigKeysDataProvider(): Generator
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

        yield 'Case DEFAULT overrides #1.' => [[
            'case'     => 'DEFAULT',
            'xml_path' => self::XML_PATH_DEFAULT,
            'env_path' => $defaultPath,
            'value'    => 'default_new_value',
        ]];
        yield 'Case DEFAULT overrides #2.' => [[
            'case'     => 'DEFAULT',
            'xml_path' => 'default/general/foo-bar/name',
            'env_path' => $defaultPathWithDash,
            'value'    => 'baz',
        ]];
        yield 'Case DEFAULT overrides #3.' => [[
            'case'     => 'DEFAULT',
            'xml_path' => 'default/general/foo_bar/name',
            'env_path' => $defaultPathWithUnderscore,
            'value'    => 'baz',
        ]];
        yield 'Case STORE overrides #1.' => [[
            'case'     => 'STORE',
            'xml_path' => self::XML_PATH_STORE,
            'env_path' => $storePath,
            'value'    => 'store_new_value',
        ]];
        yield 'Case STORE overrides #2.' => [[
            'case'     => 'STORE',
            'xml_path' => 'stores/german-at/general/store_information/name',
            'env_path' => $storeWithDashPath,
            'value'    => 'store_new_value',
        ]];
        yield 'Case STORE overrides #3.' => [[
            'case'     => 'STORE',
            'xml_path' => 'stores/german_ch/general/store_information/name',
            'env_path' => $storeWithUnderscorePath,
            'value'    => 'store_new_value',
        ]];
        yield 'Case WEBSITE overrides #1.' => [[
            'case'     => 'WEBSITE',
            'xml_path' => self::XML_PATH_WEBSITE,
            'env_path' => $websitePath,
            'value'    => 'website_new_value',
        ]];
        yield 'Case WEBSITE overrides #2.' => [[
            'case'     => 'WEBSITE',
            'xml_path' => 'websites/base_ch/general/store_information/name',
            'env_path' => $websiteWithUnderscorePath,
            'value'    => 'website_new_value',
        ]];
        yield 'Case WEBSITE overrides #3.' => [[
            'case'     => 'WEBSITE',
            'xml_path' => 'websites/base-at/general/store_information/name',
            'env_path' => $websiteWithDashPath,
            'value'    => 'website_new_value',
        ]];
    }

    /**
     * @dataProvider envDoesNotOverrideOnWrongConfigKeysDataProvider
     * @group Mage_Core
     *
     * @param array<string, string> $config
     */
    public function testEnvDoesNotOverrideForInvalidConfigKeys(array $config): void
    {
        $xmlStruct = $this->getTestXml();

        $xmlDefault = new Varien_Simplexml_Config();
        $xmlDefault->loadString($xmlStruct);
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);

        $defaultValue = 'test_default';
        $this->assertSame($defaultValue, (string) $xml->getNode(self::XML_PATH_DEFAULT));
        $defaultWebsiteValue = 'test_website';
        $this->assertSame($defaultWebsiteValue, (string) $xml->getNode(self::XML_PATH_WEBSITE));
        $defaultStoreValue = 'test_store';
        $this->assertSame($defaultStoreValue, (string) $xml->getNode(self::XML_PATH_STORE));

        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        $loader->setEnvStore([
            $config['path'] => $config['value'],
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
        $this->assertStringNotContainsString((string) $valueAfterCheck, 'value_will_not_be_changed', 'Default value was wrongfully overridden.');
    }

    public function envDoesNotOverrideOnWrongConfigKeysDataProvider(): Generator
    {
        $defaultPath = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__ST';
        $websitePath = 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__ST';
        $storePath = 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__ST';

        yield 'Case DEFAULT with ' . $defaultPath . ' will not override.' => [[
            'case'  => 'DEFAULT',
            'path'  => $defaultPath,
            'value' => 'default_value_will_not_be_changed',
        ]];
        yield 'Case WEBSITE with ' . $websitePath . ' will not override.' => [[
            'case'  => 'WEBSITE',
            'path'  => $storePath,
            'value' => 'website_value_will_not_be_changed',
        ]];
        yield 'Case STORE with ' . $storePath . ' will not override.' => [[
            'case'  => 'STORE',
            'path'  => $storePath,
            'value' => 'store_value_will_not_be_changed',
        ]];
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
