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

use Mage;
use Mage_Log_Helper_Data;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    /**
     * @var Mage_Log_Helper_Data
     */
    public Mage_Log_Helper_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('log/data');
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsVisitorLogEnabled(): void
    {
        $this->assertIsBool($this->subject->isVisitorLogEnabled());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsLogEnabled(): void
    {
        $this->assertIsBool($this->subject->isLogEnabled());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsLogDisabled(): void
    {
        $this->assertIsBool($this->subject->isLogDisabled());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Helper
     */
    public function testIsLogFileExtensionValid(): void
    {
        $this->assertIsBool($this->subject->isLogFileExtensionValid('invalid.file'));
        $this->assertIsBool($this->subject->isLogFileExtensionValid('valid.log'));
    }
 }
