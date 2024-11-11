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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Config\Form\Fieldset\Modules;

use Generator;
use Mage;
use Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput;
use PHPUnit\Framework\TestCase;

class DisableOutputTest extends TestCase
{
    public Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput();
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testGetModulesCount(): void
    {
        $this->assertSame(60, count($this->subject->getModules()));
    }

    /**
     * @dataProvider provideModules
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testGetModules(string $module): void
    {
        $result = $this->subject->getModules();
        $this->assertArrayHasKey($module, array_flip($result));
    }

    public function provideModules(): Generator
    {
        yield 'Mage_Admin' => [
          'Mage_Admin'
        ];
        yield 'Mage_Core' => [
          'Mage_Core'
        ];
    }
}
