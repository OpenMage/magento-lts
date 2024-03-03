<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Core\Helper;

use PHPUnit\Framework\TestCase;
use Mage_Core_Helper_EnvironmentConfigLoader;
use Mage_Core_Model_Config;

class EnvironmentConfigLoaderTest extends TestCase
{
    public function setup(): void
    {
        \Mage::setRoot('');
    }

    public function testBuildPath()
    {
        $environmentConfigLoaderHelper = new Mage_Core_Helper_EnvironmentConfigLoader();
        $path = $environmentConfigLoaderHelper->buildPath('GENERAL', 'STORE_INFORMATION', 'NAME');
        $this->assertEquals('general/store_information/name', $path);
    }

    public function testBuildNodePath()
    {
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        $nodePath = $loader->buildNodePath('DEFAULT', 'general/store_information/name');
        $this->assertEquals('default/general/store_information/name', $nodePath);
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

        $this->assertEquals('test_default', (string)$xml->getNode('default/general/store_information/name'));
        $this->assertEquals('test_website', (string)$xml->getNode('websites/base/general/store_information/name'));
        $this->assertEquals('test_store', (string)$xml->getNode('stores/german/general/store_information/name'));

        // act
        $loader = new Mage_Core_Helper_EnvironmentConfigLoader();
        $loader->setEnvStore([
            $config['path'] => $config['value']
        ]);
        $loader->overrideEnvironment($xml);

        switch ($config['case']) {
            case 'DEFAULT':
                $defaultValue = $xmlDefault->getNode('default/general/store_information/name');
                $valueAfterOverride = $xml->getNode('default/general/store_information/name');
                break;
            case 'STORE':
                $defaultValue = $xmlDefault->getNode('stores/german/general/store_information/name');
                $valueAfterOverride = $xml->getNode('stores/german/general/store_information/name');
                break;
            case 'WEBSITE':
                $defaultValue = $xmlDefault->getNode('websites/base/general/store_information/name');
                $valueAfterOverride = $xml->getNode('websites/base/general/store_information/name');
                break;
        }

        // assert
        $this->assertNotEquals((string)$defaultValue, (string)$valueAfterOverride, 'Default value was not overridden.');
    }

    public function env_overrides_correct_config_keys(): array
    {
        $defaultPath = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME';
        $websitePath = 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__STORE_INFORMATION__NAME';
        $storePath = 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME';
        return [
            [
                'Case DEFAULT with ' . $defaultPath . ' overrides.' => [
                    'case'  => 'DEFAULT',
                    'path'  => $defaultPath,
                    'value' => 'default_new_value'
                ]
            ],
            [
                'Case STORE with ' . $storePath . ' overrides.' => [
                    'case'  => 'STORE',
                    'path'  => $storePath,
                    'value' => 'store_new_value'
                ]
            ],
            [
                'Case WEBSITE with ' . $websitePath . ' overrides.' => [
                    'case'  => 'WEBSITE',
                    'path'  => $websitePath,
                    'value' => 'website_new_value'
                ]
            ]
        ];
    }

    /**
     * @dataProvider env_does_not_override_on_wrong_config_keys
     * @test
     */
    public function env_does_not_override_for_valid_config_keys(array $config)
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
    </websites>
    <stores>
        <german>
            <general>
                <store_information>
                        <name>test_store</name>
                </store_information>
            </general>
        </german>
    </stores>
</config>
XML;
    }
}
