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
use Mage_Core_Block_Abstract;
use Mage_Core_Block_Template;
use Mage_Core_Exception;
use Mage_Core_Helper_Security as Subject;
use Mage_Page_Block_Html_Topmenu_Renderer;
use PHPUnit\Framework\TestCase;

use function sprintf;

class SecurityTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/security');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
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
     * @param string[] $args
     * @throws Mage_Core_Exception
     *
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateAgainstBlockMethodBlacklist(
        Mage_Core_Block_Abstract $block,
        string $method,
        array $args
    ): void {
        $this->subject->validateAgainstBlockMethodBlacklist($block, $method, $args);
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
     * @param string[] $args
     * @throws Mage_Core_Exception
     *
     * @group Mage_Core
     * @group Mage_Core_Helper
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
