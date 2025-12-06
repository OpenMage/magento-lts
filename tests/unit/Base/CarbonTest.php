<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Varien_Date;

final class CarbonTest extends TestCase
{
    /**
     * @group Carbon
     */
    public function testDate(): void
    {
        $timestamp = mktime(0, 0, 0, 1, 2, random_int(2006, 2007));
        self::assertSame(
            date(Varien_Date::DATETIME_PHP_FORMAT, $timestamp),
            Carbon::createFromTimestamp($timestamp)->format(Varien_Date::DATETIME_PHP_FORMAT),
        );
    }

    /**
     * @group Carbon
     */
    public function testTime(): void
    {
        self::assertSame(time(), Carbon::now()->getTimestamp());
    }

    /**
     * @group Carbon
     */
    public function testStrtotime(): void
    {
        $dateString = '2024-01-15 14:30:00';
        self::assertSame(strtotime($dateString), Carbon::parse($dateString)->getTimestamp());
    }
}
