<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Core\Helper;

use PHPUnit\Framework\TestCase;
use Mage_Core_Helper_EnvironmentConfigLoader;
use Mage_Core_Model_Config;

class EnvironmentConfigLoaderTest extends TestCase
{
    protected const ENV_CONFIG_DEFAULT_PATH = 'OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME';
    protected const ENV_CONFIG_DEFAULT_VALUE = 'default_new_value';
    protected const ENV_CONFIG_WEBSITE_PATH = 'OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__STORE_INFORMATION__NAME';
    protected const ENV_CONFIG_WEBSITE_VALUE = 'website_new_value';
    protected const ENV_CONFIG_STORE_PATH = 'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME';
    protected const ENV_CONFIG_STORE_VALUE = 'store_german_new_value';

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
     * @dataProvider envOverridesDataProvider
     *
     */
    public function testEnvOverrides(array $config)
    {
        error_reporting(0);
        $xmlStruct = $this->getTestXml();

        $xmlDefault = new Mage_Core_Model_Config($xmlStruct);
        $xmlDefault = $xmlDefault->loadModulesConfiguration('config.xml', $xmlDefault);
        $xml = new Mage_Core_Model_Config($xmlStruct);
        $xml = $xml->loadModulesConfiguration('config.xml', $xml);

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
                $valueAfterOverride = $xml->getNode('general/store_information/name');
                break;
            case 'WEBSITE':
                $defaultValue = $xmlDefault->getNode('default/general/store_information/name');
                $valueAfterOverride = $xml->getNode('website/base/store_information/name');
                break;
        }

        // assert
        $this->assertNotEquals((string)$defaultValue, (string)$valueAfterOverride, 'Default value was not overridden.');
    }

    public function envOverridesDataProvider(): array
    {
        return [
            [
                'Case DEFAULT with ' . static::ENV_CONFIG_DEFAULT_PATH . ' overrides.' => [
                    'case'  => 'DEFAULT',
                    'path'  => static::ENV_CONFIG_DEFAULT_PATH,
                    'value' => static::ENV_CONFIG_DEFAULT_VALUE
                ]
            ],
            [
                'Case STORE with ' . static::ENV_CONFIG_STORE_PATH . ' overrides.' => [
                    'case'  => 'STORE',
                    'path'  => static::ENV_CONFIG_STORE_PATH,
                    'value' => static::ENV_CONFIG_STORE_VALUE
                ]
            ],
            [
                'Case WEBSITE with ' . static::ENV_CONFIG_WEBSITE_PATH . ' overrides.' => [
                    'case'  => 'WEBSITE',
                    'path'  => static::ENV_CONFIG_WEBSITE_PATH,
                    'value' => static::ENV_CONFIG_WEBSITE_VALUE
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
    <modules>
        <Mage_Core>
            <active>true</active>
            <codePool>core</codePool>
        </Mage_Core>
    </modules>
    
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
