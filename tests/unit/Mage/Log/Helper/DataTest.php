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

namespace OpenMage\Tests\Unit\Mage\Log\Helper;

use Generator;
use Mage;
use Mage_Log_Helper_Data as Subject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('log/data');
    }

    /**
     * @covers Mage_Log_Helper_Data::isVisitorLogEnabled()
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsVisitorLogEnabled(): void
    {
        $this->assertTrue($this->subject->isVisitorLogEnabled());
    }

    /**
     * @covers Mage_Log_Helper_Data::isLogEnabled()
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsLogEnabled(): void
    {
        $this->assertFalse($this->subject->isLogEnabled());
    }

    /**
     * @covers Mage_Log_Helper_Data::isLogDisabled()
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsLogDisabled(): void
    {
        $this->assertFalse($this->subject->isLogDisabled());
    }

    /**
     * @covers Mage_Log_Helper_Data::isLogFileExtensionValid()
     * @dataProvider provideIsLogFileExtensionValid
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsLogFileExtensionValid(bool $expectedResult, string $file): void
    {
        $this->assertSame($expectedResult, $this->subject->isLogFileExtensionValid($file));
    }

    public function provideIsLogFileExtensionValid(): Generator
    {
        yield 'valid' => [
            true,
            'valid.log',
        ];
        yield 'invalid' => [
            false,
            'invalid.file',
        ];
    }
}
