<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports;

use Carbon\Carbon;
use Generator;
use Mage_Reports_Helper_Data as Helper;

trait ReportsTrait
{
    public static string $dateFirstDay  = '2025-01-01';
    public static string $dateNextDay   = '2025-01-02';
    public static string $dateLastDay   = '2025-12-31';

    public function provideReportsDateIntervals(): Generator
    {
        $prefix = Helper::REPORT_PERIOD_TYPE_DAY . ': ';

        yield $prefix . 'no from/to' => [
            0,
            '',
            '',
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'no from' => [
            0,
            '',
            self::$dateFirstDay,
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];

        $date = Carbon::parse(self::$dateFirstDay);
        $now = Carbon::now();
        $diff = $date->diffInDays($now);

        yield $prefix . 'no to' => [
            $diff + 1, // +1 because we include the first day
            self::$dateFirstDay,
            '',
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];

        yield $prefix . 'same day' => [
            1,
            self::$dateFirstDay,
            self::$dateFirstDay,
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'same and next day' => [
            2,
            self::$dateFirstDay,
            self::$dateNextDay,
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'same and previous day' => [
            0,
            self::$dateNextDay,
            self::$dateFirstDay,
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2025' => [
            365,
            self::$dateFirstDay,
            self::$dateLastDay,
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2026' => [
            365,
            '2026-01-01',
            '2026-12-31',
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2027' => [
            365,
            '2027-01-01',
            '2027-12-31',
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2028' => [
            366,
            '2028-01-01',
            '2028-12-31',
            Helper::REPORT_PERIOD_TYPE_DAY,
        ];

        $prefix = Helper::REPORT_PERIOD_TYPE_MONTH . ': ';

        yield $prefix . 'year 2025 full' => [
            12,
            self::$dateFirstDay,
            self::$dateLastDay,
            Helper::REPORT_PERIOD_TYPE_MONTH,
        ];
        yield $prefix . 'year 2025, half january' => [
            12,
            '2025-01-15',
            self::$dateLastDay,
            Helper::REPORT_PERIOD_TYPE_MONTH,
        ];
        yield $prefix . 'year 2025, half december' => [
            12,
            self::$dateFirstDay,
            '2025-12-15',
            Helper::REPORT_PERIOD_TYPE_MONTH,
        ];
        $prefix = Helper::REPORT_PERIOD_TYPE_YEAR . ': ';

        yield $prefix . 'same year' => [
            1,
            self::$dateFirstDay,
            self::$dateLastDay,
            Helper::REPORT_PERIOD_TYPE_YEAR,
        ];
        yield $prefix . 'year 2025 and next' => [
            2,
            '2025-01-15',
            '2026-12-31',
            Helper::REPORT_PERIOD_TYPE_YEAR,
        ];
        yield $prefix . 'year 2025, half december 2026' => [
            2,
            self::$dateFirstDay,
            '2026-12-15',
            Helper::REPORT_PERIOD_TYPE_YEAR,
        ];
    }
}
