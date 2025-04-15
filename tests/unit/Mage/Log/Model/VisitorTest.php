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

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Visitor as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class VisitorTest extends OpenMageTest
{
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = Mage::getModel('log/visitor');
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testInitServerData(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->initServerData());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetOnlineMinutesInterval(): void
    {
        static::assertIsInt(self::$subject->getOnlineMinutesInterval());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetUrl(): void
    {
        static::assertIsString(self::$subject->getUrl());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFirstVisitAt(): void
    {
        static::assertIsString(self::$subject->getFirstVisitAt());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetLastVisitAt(): void
    {
        static::assertIsString(self::$subject->getLastVisitAt());
    }
}
