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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block;

use Generator;
use Mage;
use Mage_Adminhtml_Block_Template;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public Mage_Adminhtml_Block_Template $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Mage_Adminhtml_Block_Template();
    }

    /**
     * @see Mage_Core_Model_Session::getFormKey()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFormKey(): void
    {
        $this->assertIsString($this->subject->getFormKey());
    }

    /**
     * @covers Mage_Adminhtml_Block_Template::isOutputEnabled()
     * @dataProvider provideIsOutputEnabled
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testIsOutputEnabled(bool $expectedResult, ?string $moduleName): void
    {
        $this->assertSame($expectedResult, $this->subject->isOutputEnabled($moduleName));
    }

    public function provideIsOutputEnabled(): Generator
    {
        yield 'null' => [
            true,
            null, #Mage_Adminhtml
        ];
        yield 'Mage_Core' => [
            true,
            'Mage_Core',
        ];
        yield 'Not_Exist' => [
            false,
            'Not_Exist',
        ];
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testGetModuleName(): void
    {
        $this->assertSame('Mage_Adminhtml', $this->subject->getModuleName());
    }

    /**
     * @see Mage_Core_Model_Input_Filter_MaliciousCode::filter()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testMaliciousCodeFilter(): void
    {
        $this->assertIsString($this->subject->maliciousCodeFilter(''));
    }
}
