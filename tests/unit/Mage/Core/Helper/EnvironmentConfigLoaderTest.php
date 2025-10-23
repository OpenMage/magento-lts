<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Helper_EnvironmentConfigLoader;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Simplexml_Config;

/**
 * @group Mage_Core_EnvLoader
 */
class EnvironmentConfigLoaderTest extends OpenMageTest
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
     * @group Helper
     */
    public function testBuildPath(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        $path = $environmentConfigLoaderHelper->exposedBuildPath('GENERAL', 'STORE_INFORMATION', 'NAME');
        self::assertSame(self::XML_PATH_GENERAL, $path);
    }

    /**
     * @group Helper
     */
    public function testEnvFilter(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        /** @phpstan-ignore method.internal */
        $environmentConfigLoaderHelper->setEnvStore([
            'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME' => 'some_value',
        ]);
        // empty because env flag is not set
        $env = $environmentConfigLoaderHelper->getEnv();
        static::assertIsArray($env);
        static::assertEmpty($env);
        /** @phpstan-ignore method.internal */
        $environmentConfigLoaderHelper->setEnvStore([
            'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME' => 'some_value',
            'OPENMAGE_CONFIG_OVERRIDE_ALLOWED' => 1, // enable feature
        ]);
        // flag is set => feature is enabled
        $env = $environmentConfigLoaderHelper->getEnv();
        static::assertIsArray($env);
        static::assertNotEmpty($env);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testBuildNodePath(): void
    {
        $environmentConfigLoaderHelper = new EnvironmentConfigLoaderTestHelper();
        $nodePath = $environmentConfigLoaderHelper->exposedBuildNodePath('DEFAULT', self::XML_PATH_GENERAL);
        self::assertSame(self::XML_PATH_DEFAULT, $nodePath);
    }

    /**
     * @group Helper
     */
    public function testXmlHasTestStrings(): void
    {
        $xmlStruct = $this->getTestXml();
        $xml = new Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);
        self::assertSame('test_default', (string) $xml->getNode(self::XML_PATH_DEFAULT));
        self::assertSame('test_website', (string) $xml->getNode(self::XML_PATH_WEBSITE));
        self::assertSame('test_store', (string) $xml->getNode(self::XML_PATH_STORE));
    }

    /**
     * @runInSeparateProcess
     * @dataProvider envOverridesCorrectConfigKeysDataProvider
     * @group Helper
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


        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        /** @phpstan-ignore method.internal */
        $loader->setEnvStore([
            'OPENMAGE_CONFIG_OVERRIDE_ALLOWED' => 1,
            $config['env_path'] => $config['value'],
        ]);
        Mage::unregister('current_env_config');
        $loader->overrideEnvironment($xml);

        $configPath = $config['xml_path'];
        $defaultValue = $xmlDefault->getNode($configPath);
        $valueAfterOverride = $xml->getNode($configPath);

        // assert
        $expected = (string) $defaultValue;
        $actual = (string) $valueAfterOverride;
        static::assertNotSame($expected, $actual, 'Default value was not overridden.');
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
     * @runInSeparateProcess
     * @dataProvider envAsArrayDataProvider
     * @group Mage_Core
     *
     * @param array<string, string> $config
     */
    public function testAsArray(array $config): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        /** @phpstan-ignore method.internal */
        $loader->setEnvStore([
            'OPENMAGE_CONFIG_OVERRIDE_ALLOWED' => 1,
            $config['env_path'] => 1,
        ]);
        $store = $config['store'];
        $actual = $loader->getAsArray($store);
        $expected = $config['expected'];
        static::assertSame($expected, $actual);
    }

    public function envAsArrayDataProvider(): Generator
    {
        yield 'default' => [
            [
                'env_path'  => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME',
                'store'  => '', // or 'default', which will be used internally, but this is how \Mage_Adminhtml_Model_Config_Data::_validate defines it
                'expected'  => [
                    'general/store_information/name' => 1,
                ],
            ],
        ];
        yield 'store' => [
            [
                'env_path'  => 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME',
                'store'  => 'german',
                'expected'  => [
                    'general/store_information/name' => 1,
                ],
            ],
        ];
        yield 'invalidStore' => [
            [
                'env_path'  => '',
                'store'  => 'foo',
                'expected'  => [],
            ],
        ];
    }

    /**
     * @runInSeparateProcess
     * @dataProvider envHasPathDataProvider
     * @group Mage_Core
     *
     * @param array<string, string> $config
     */
    public function testHasPath(array $config): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        /** @phpstan-ignore method.internal */
        $loader->setEnvStore([
            'OPENMAGE_CONFIG_OVERRIDE_ALLOWED' => 1,
            $config['env_path'] => 1,
        ]);
        $actual = $loader->hasPath($config['xml_path']);
        $expected = $config['expected'];
        static::assertSame($expected, $actual);
    }

    public function envHasPathDataProvider(): Generator
    {
        yield 'hasPath default' => [
            [
                'env_path'  => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME',
                'xml_path'  => 'default/general/store_information/name',
                'expected'  => true,
            ],
        ];
        yield 'hasPath store' => [
            [
                'env_path'  => 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME',
                'xml_path'  => 'stores/german/general/store_information/name',
                'expected'  => true,
            ],
        ];
        yield 'hasNotPath' => [
            [
                'env_path'  => 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME',
                'xml_path'  => 'foo/foo/foo',
                'expected'  => false,
            ],
        ];
    }

    /**
     * @runInSeparateProcess
     * @dataProvider envDoesNotOverrideOnWrongConfigKeysDataProvider
     * @group Helper
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
        $actual = (string) $xml->getNode(self::XML_PATH_DEFAULT);
        static::assertSame($defaultValue, $actual);
        $defaultWebsiteValue = 'test_website';
        $actual = (string) $xml->getNode(self::XML_PATH_WEBSITE);
        static::assertSame($defaultWebsiteValue, $actual);
        $defaultStoreValue = 'test_store';
        $actual = (string) $xml->getNode(self::XML_PATH_STORE);
        static::assertSame($defaultStoreValue, $actual);

        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        /** @phpstan-ignore method.internal */
        $loader->setEnvStore([
            'OPENMAGE_CONFIG_OVERRIDE_ALLOWED' => 1,
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
        self::assertStringNotContainsString((string) $valueAfterCheck, 'value_will_not_be_changed', 'Default value was wrongfully overridden.');
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
