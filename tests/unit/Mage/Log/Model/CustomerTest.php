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
use Mage_Log_Model_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class CustomerTest extends OpenMageTest
{
    /** @phpstan-ignore property.onlyWritten */
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('log/customer');
    }

    /**
     * @group Model
     */
    public function testGetLoginAtTimestamp(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getLoginAt'])
            ->getMock();

        static::assertNull($mock->getLoginAtTimestamp());

        $mock->method('getLoginAt')->willReturn(true);
        static::assertIsInt($mock->getLoginAtTimestamp());
    }
}
