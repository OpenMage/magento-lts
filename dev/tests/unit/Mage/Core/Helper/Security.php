<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Core\Helper;

use PHPUnit\Framework\TestCase;

class Security extends TestCase
{

    public function validateAgainstBlockMethodBlacklistDataProvider()
    {
        $topmenu = new \Mage_Page_Block_Html_Topmenu_Renderer();
        $template = new \Mage_Core_Block_Template();

        return [
            [
                $topmenu,
                'setData',
                []
            ],
            [
                $template,
                'setData',
                []
            ],
        ];
    }

    /**
     * @dataProvider validateAgainstBlockMethodBlacklistDataProvider
     * @doesNotPerformAssertions if data is correct, then NO exception is thrown, so we don't need an assertion
     * @return void
     */
    public function testValidateAgainstBlockMethodBlacklist($block, $method, $args)
    {
        $securityHelper = new \Mage_Core_Helper_Security();
        $securityHelper->validateAgainstBlockMethodBlacklist($block, $method, $args);
    }


    public function forbiddenBlockMethodsDataProvider()
    {
        $topmenu = new \Mage_Page_Block_Html_Topmenu_Renderer();
        $template = new \Mage_Core_Block_Template();

        return [
            [
                $template,
                'fetchView',
                []
            ],
            [
                $topmenu,
                'fetchView',
                []
            ],
            [
                $topmenu,
                'render',
                []
            ],
            [
                $template,
                'Mage_Core_Block_Template::fetchView',
                []
            ],
            'parent class name is passed as second arg' => [
                $topmenu,
                'Mage_Core_Block_Template::fetchView',
                []
            ],
        ];
    }

    /**
     * @dataProvider forbiddenBlockMethodsDataProvider
     * @return void
     */
    public function testValidateAgainstBlockMethodBlacklistThrowsException($block, $method, $args)
    {
        $this->expectExceptionMessage(\sprintf('Action with combination block %s and method %s is forbidden.', get_class($block), $method));

        $securityHelper = new \Mage_Core_Helper_Security();
        $securityHelper->validateAgainstBlockMethodBlacklist($block, $method, $args);
    }
}