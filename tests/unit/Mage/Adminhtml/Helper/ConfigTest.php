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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Generator;
use Mage;
use Mage_Adminhtml_Helper_Config as Subject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/config');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Config::getInputTypes()
     * @dataProvider provideGetInputTypes
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetInputTypes(array $expectedResult, ?string $inputType): void
    {
        $this->assertSame($expectedResult, $this->subject->getInputTypes($inputType));
    }

    public function provideGetInputTypes(): Generator
    {
        yield 'null' => [
            [
                'color' => [
                    'backend_model' => 'adminhtml/system_config_backend_color',
                ],
            ],
            null,
        ];
        yield 'color' => [
            [
                'backend_model' => 'adminhtml/system_config_backend_color',
            ],
            'color',
        ];
        yield 'invalid' => [
            [],
            'invalid',
        ];
    }

    /**
     * @covers Mage_Adminhtml_Helper_Config::getBackendModelByInputType()
     * @dataProvider provideGetBackendModelByInputType
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetBackendModelByInputType(?string $expectedResult, string $inputType): void
    {
        $this->assertSame($expectedResult, $this->subject->getBackendModelByInputType($inputType));
    }

    public function provideGetBackendModelByInputType(): Generator
    {
        yield 'color' => [
            'adminhtml/system_config_backend_color',
            'color',
        ];
        yield 'invalid' => [
            null,
            'invalid',
        ];
    }
}
