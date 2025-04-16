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
use Mage_Log_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Log\LogTrait;

class DataTest extends OpenMageTest
{
    use LogTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('log/data');
    }

    /**
     * @covers Mage_Log_Helper_Data::isVisitorLogEnabled()
     * @group Helper
     */
    public function testIsVisitorLogEnabled(): void
    {
        static::assertTrue(self::$subject->isVisitorLogEnabled());
    }

    /**
     * @covers Mage_Log_Helper_Data::isLogEnabled()
     * @group Helper
     */
    public function testIsLogEnabled(): void
    {
        static::assertFalse(self::$subject->isLogEnabled());
    }

    /**
     * @covers Mage_Log_Helper_Data::isLogDisabled()
     * @group Helper
     */
    public function testIsLogDisabled(): void
    {
        static::assertFalse(self::$subject->isLogDisabled());
    }

    /**
     * @covers Mage_Log_Helper_Data::isLogFileExtensionValid()
     * @dataProvider provideIsLogFileExtensionValid
     * @group Helper
     */
    public function testIsLogFileExtensionValid(bool $expectedResult, string $file): void
    {
        static::assertSame($expectedResult, self::$subject->isLogFileExtensionValid($file));
    }
}
