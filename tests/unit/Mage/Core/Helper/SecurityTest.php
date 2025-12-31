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
use Mage_Core_Block_Abstract;
use Mage_Core_Block_Template;
use Mage_Core_Exception;
use Mage_Core_Helper_Security as Subject;
use Mage_Page_Block_Html_Topmenu_Renderer;
use OpenMage\Tests\Unit\OpenMageTest;

use function sprintf;

final class SecurityTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/security');
    }

    /**
     * @group Helper
     */
    public function validateAgainstBlockMethodBlacklistDataProvider(): Generator
    {
        $topmenu = new Mage_Page_Block_Html_Topmenu_Renderer();
        $template = new Mage_Core_Block_Template();
        yield [
            $topmenu,
            'setData',
            [],
        ];
        yield [
            $template,
            'setData',
            [],
        ];
    }

    /**
     * @dataProvider validateAgainstBlockMethodBlacklistDataProvider
     * @doesNotPerformAssertions if data is correct, then NO exception is thrown, so we don't need an assertion
     * @param  string[]            $args
     * @throws Mage_Core_Exception
     *
     * @group Helper
     */
    public function testValidateAgainstBlockMethodBlacklist(
        Mage_Core_Block_Abstract $block,
        string $method,
        array $args
    ): void {
        self::$subject->validateAgainstBlockMethodBlacklist($block, $method, $args);
    }

    public function forbiddenBlockMethodsDataProvider(): Generator
    {
        $topmenu = new Mage_Page_Block_Html_Topmenu_Renderer();
        $template = new Mage_Core_Block_Template();
        yield [
            $template,
            'fetchView',
            [],
        ];
        yield [
            $topmenu,
            'fetchView',
            [],
        ];
        yield [
            $topmenu,
            'render',
            [],
        ];
        yield [
            $template,
            'Mage_Core_Block_Template::fetchView',
            [],
        ];
        yield [
            $topmenu,
            'Mage_Page_Block_Html_Topmenu_Renderer::fetchView',
            [],
        ];
        yield 'parent class name is passed as second arg' => [
            $topmenu,
            'Mage_Core_Block_Template::fetchView',
            [],
        ];
        yield 'parent class name is passed as second arg2' => [
            $topmenu,
            'Mage_Core_Block_Template::render',
            [],
        ];
    }

    /**
     * @dataProvider forbiddenBlockMethodsDataProvider
     * @param  string[]            $args
     * @throws Mage_Core_Exception
     *
     * @group Helper
     */
    public function testValidateAgainstBlockMethodBlacklistThrowsException(
        Mage_Core_Block_Abstract $block,
        string $method,
        array $args
    ): void {
        $this->expectExceptionMessage(sprintf('Action with combination block %s and method %s is forbidden.', $block::class, $method));
        self::$subject->validateAgainstBlockMethodBlacklist($block, $method, $args);
    }
}
