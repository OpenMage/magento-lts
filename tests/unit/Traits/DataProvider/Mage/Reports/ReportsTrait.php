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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports;

use Generator;
use Mage_Reports_Helper_Data;

trait ReportsTrait
{
    public function provideReportsDateIntervals(): Generator
    {
        $prefix = Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY . ': ';

        yield $prefix . 'no from/to' => [
            0,
            '',
            '',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'no from' => [
            0,
            '',
            '2025-01-01',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];

        if (!defined('DATA_MAY_CHANGED')) {
            yield $prefix . 'no to' => [
                0,
                '2025-01-01',
                '',
                Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
            ];
        }

        yield $prefix . 'same day' => [
            1,
            '2025-01-01',
            '2025-01-01',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'same and next day' => [
            2,
            '2025-01-01',
            '2025-01-02',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'same and previous day' => [
            0,
            '2025-01-02',
            '2025-01-01',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2025' => [
            365,
            '2025-01-01',
            '2025-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2026' => [
            365,
            '2026-01-01',
            '2026-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2027' => [
            365,
            '2027-01-01',
            '2027-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];
        yield $prefix . 'year 2028' => [
            366,
            '2028-01-01',
            '2028-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY,
        ];

        $prefix = Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_MONTH . ': ';

        yield $prefix . 'year 2025 full' => [
            12,
            '2025-01-01',
            '2025-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_MONTH,
        ];
        yield $prefix . 'year 2025, half january' => [
            12,
            '2025-01-15',
            '2025-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_MONTH,
        ];
        yield $prefix . 'year 2025, half december' => [
            12,
            '2025-01-01',
            '2025-12-15',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_MONTH,
        ];
        $prefix = Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_YEAR . ': ';

        yield $prefix . 'same year' => [
            0,
            '2025-01-01',
            '2025-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_YEAR,
        ];
        yield $prefix . 'year 2025 and next' => [
            1,
            '2025-01-15',
            '2026-12-31',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_YEAR,
        ];
        yield $prefix . 'year 2025, half december 2026' => [
            1,
            '2025-01-01',
            '2026-12-15',
            Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_YEAR,
        ];
    }
}
