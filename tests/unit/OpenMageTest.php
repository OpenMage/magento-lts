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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit;

use Mage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class LongRunningTestAlert
 */
abstract class OpenMageTest extends TestCase
{
    public const WILL_RETURN_SELF = '__willReturnSelf__';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Mage::app();
    }

    /**
     * @param class-string $class
     */
    public function getMockWithCalledMethods(string $class, array $methods, ?bool $expectOnce =  false): MockObject
    {
        $mock = $this->getMockBuilder($class)
            ->setMethods(array_keys($methods))
            ->getMock();

        if (is_null($expectOnce)) {
            return $mock;
        }

        foreach ($methods as $key => $value) {
            if ($expectOnce) {
                $mockMethod = $mock->expects(static::once())->method($key);
            } else {
                $mockMethod = $mock->method($key);
            }

            if ($value === self::WILL_RETURN_SELF) {
                $mockMethod->willReturnSelf();
            } else {
                $mockMethod->willReturn($value);
            }
        }

        return $mock;
    }
}
