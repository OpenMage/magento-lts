<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Core\Helper;

use PHPUnit\Framework\TestCase;
use Mage_Core_Helper_EnvironmentConfigLoader;
use Mage_Core_Model_Config;

class TestEnvLoaderHelper extends Mage_Core_Helper_EnvironmentConfigLoader {
    public function exposedBuildPath(string $section, string $group, string $field): string
    {
        return $this->buildPath($section, $group, $field);
    }

    public function exposedBuildNodePath(string $scope, string $path): string
    {
        return $this->buildNodePath($scope, $path);
    }
}

class EnvironmentConfigLoaderTest extends TestCase
{
    public function setup(): void
    {
        \Mage::setRoot('');
    }

    public function testBuildPath()
    {
        $environmentConfigLoaderHelper = new TestEnvLoaderHelper();
        $path = $environmentConfigLoaderHelper->exposedBuildPath('GENERAL', 'STORE_INFORMATION', 'NAME');
        $this->assertEquals('general/store_information/name', $path);
    }

    public function testBuildNodePath()
    {
        $environmentConfigLoaderHelper = new TestEnvLoaderHelper();
        $nodePath = $environmentConfigLoaderHelper->exposedBuildNodePath('DEFAULT', 'general/store_information/name');
        $this->assertEquals('default/general/store_information/name', $nodePath);
    }

    public function test_xml_has_test_strings()
    {
        $xmlStruct = $this->getTestXml();
        $xml = new \Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);
        $this->assertEquals('test_default', (string)$xml->getNode('default/general/store_information/name'));
        $this->assertEquals('test_website', (string)$xml->getNode('websites/base/general/store_information/name'));
        $this->assertEquals('test_store', (string)$xml->getNode('stores/german/general/store_information/name'));
    }

    /**
     * @dataProvider env_overrides_correct_config_keys
     * @test
     */
    public function env_overrides_for_valid_config_keys(array $config)
    {
        $xmlStruct = $this->getTestXml();

        $xmlDefault = new \Varien_Simplexml_Config();
        $xmlDefault->loadString($xmlStruct);
        $xml = new \Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);

        // act
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

    public function env_overrides_correct_config_keys(): array
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
                    'xml_path' => 'default/general/store_information/name',
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
                    'xml_path' => 'stores/german/general/store_information/name',
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
                    'xml_path' => 'websites/base/general/store_information/name',
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
     * @dataProvider env_does_not_override_on_wrong_config_keys
     * @test
     */
    public function env_does_not_override_for_invalid_config_keys(array $config)
    {
        $xmlStruct = $this->getTestXml();

        $xmlDefault = new \Varien_Simplexml_Config();
        $xmlDefault->loadString($xmlStruct);
        $xml = new \Varien_Simplexml_Config();
        $xml->loadString($xmlStruct);

        $defaultValue = 'test_default';
        $this->assertEquals($defaultValue, (string)$xml->getNode('default/general/store_information/name'));
        $defaultWebsiteValue = 'test_website';
        $this->assertEquals($defaultWebsiteValue, (string)$xml->getNode('websites/base/general/store_information/name'));
        $defaultStoreValue = 'test_store';
        $this->assertEquals($defaultStoreValue, (string)$xml->getNode('stores/german/general/store_information/name'));

        // act
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        $loader->setEnvStore([
            $config['path'] => $config['value']
        ]);
        $loader->overrideEnvironment($xml);

        switch ($config['case']) {
            case 'DEFAULT':
                $valueAfterCheck = $xml->getNode('default/general/store_information/name');
                break;
            case 'STORE':
                $valueAfterCheck = $xml->getNode('stores/german/general/store_information/name');
                break;
            case 'WEBSITE':
                $valueAfterCheck = $xml->getNode('websites/base/general/store_information/name');
                break;
        }

        // assert
        $this->assertTrue(!str_contains('value_will_not_be_changed', (string)$valueAfterCheck), 'Default value was wrongfully overridden.');
    }

    public function env_does_not_override_on_wrong_config_keys(): array
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
