<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Block_Abstract;
use Mage_Core_Block_Template;
use Mage_Core_Exception;
use Mage_Core_Helper_Security;
use Mage_Page_Block_Html_Topmenu_Renderer;
use PHPUnit\Framework\TestCase;
use function sprintf;

class SecurityTest extends TestCase
{
    /**
     * @var Mage_Core_Helper_Security
     */
    public Mage_Core_Helper_Security $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/security');
    }

    /**
     * @return array<int|string, array<int, array<empty, empty>|Mage_Page_Block_Html_Topmenu_Renderer|Mage_Core_Block_Template|string>>
     */
    public function validateAgainstBlockMethodBlacklistDataProvider(): array
    {
        $topmenu = new Mage_Page_Block_Html_Topmenu_Renderer();
        $template = new Mage_Core_Block_Template();

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
     * @param Mage_Core_Block_Abstract $block
     * @param string $method
     * @param string[] $args
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testValidateAgainstBlockMethodBlacklist(
        Mage_Core_Block_Abstract $block,
        string $method,
        array $args
    ): void {
        $this->subject->validateAgainstBlockMethodBlacklist($block, $method, $args);
    }

    /**
     * @return array<int|string, array<int, array<empty, empty>|Mage_Page_Block_Html_Topmenu_Renderer|Mage_Core_Block_Template|string>>
     */
    public function forbiddenBlockMethodsDataProvider(): array
    {
        $topmenu = new Mage_Page_Block_Html_Topmenu_Renderer();
        $template = new Mage_Core_Block_Template();

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
            [
                $topmenu,
                'Mage_Page_Block_Html_Topmenu_Renderer::fetchView',
                []
            ],
            'parent class name is passed as second arg' => [
                $topmenu,
                'Mage_Core_Block_Template::fetchView',
                []
            ],
            'parent class name is passed as second arg2' => [
                $topmenu,
                'Mage_Core_Block_Template::render',
                []
            ],
        ];
    }

    /**
     * @dataProvider forbiddenBlockMethodsDataProvider
     * @param Mage_Core_Block_Abstract $block
     * @param string $method
     * @param string[] $args
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testValidateAgainstBlockMethodBlacklistThrowsException(
        Mage_Core_Block_Abstract $block,
        string $method,
        array $args
    ): void {
        $this->expectExceptionMessage(sprintf('Action with combination block %s and method %s is forbidden.', get_class($block), $method));
        $this->subject->validateAgainstBlockMethodBlacklist($block, $method, $args);
    }
}
