<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
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
            $mockMethod = $expectOnce ? $mock->expects(self::once())->method($key) : $mock->method($key);

            if ($value === self::WILL_RETURN_SELF) {
                $mockMethod->willReturnSelf();
            } else {
                $mockMethod->willReturn($value);
            }
        }

        return $mock;
    }
}
