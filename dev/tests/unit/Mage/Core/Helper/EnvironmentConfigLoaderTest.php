<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Core\Helper;

use PHPUnit\Framework\TestCase;

class Security extends TestCase
{

    public function simpleEnvValueDataProvider(): array
    {
        return [
            [
                'OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME',
                'exampleValue',
                'stores/german/general/store_information/name',
                'exampleValue'
            ],
        ];
    }

    /**
     * @dataProvider simpleEnvValueDataProvider
     */
    public function testSimpleEnvToConfig($envName, $envValue, $expectedPath, $expectedValue):void
    {
        \Mage::setRoot('');
        $helper = new \Mage_Core_Helper_EnvironmentConfigLoader();
        $config = new \Mage_Core_Model_Config(
            new \Varien_Simplexml_Element('<xml/>')
        );

        $helper->setEnvStore([
            $envName => $envValue
        ]);
        $helper->overrideEnvironment($config);

        $this->assertEquals(
            $expectedValue,
            $config->getNode($expectedPath)
        );
    }
}
