<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Block\Widget;

use Mage_Customer_Block_Widget_Dob as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DobTest extends OpenMageTest
{
    public Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Subject();
    }

    /**
     * Test that getYear() returns the calendar year, not the ISO-8601 week-numbering year.
     * Dates near the start of the year (like Jan 1) can belong to the previous ISO year.
     *
     * @group Block
     * @dataProvider provideGetYearData
     */
    public function testGetYear(string $expectedYear, string $date): void
    {
        $this->subject->setDate($date);
        self::assertSame($expectedYear, $this->subject->getYear());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideGetYearData(): array
    {
        return [
            // Jan 1, 2000 is in ISO week 52 of 1999 - must return 2000, not 1999
            'jan-1-2000' => ['2000', '2000-01-01'],
            // Jan 1, 1999 is in ISO week 53 of 1998 - must return 1999, not 1998
            'jan-1-1999' => ['1999', '1999-01-01'],
            // Jan 1, 2023 is in ISO week 52 of 2022 - must return 2023, not 2022
            'jan-1-2023' => ['2023', '2023-01-01'],
            // A mid-year date - no ambiguity
            'mid-year'   => ['2000', '2000-06-15'],
        ];
    }

    /**
     * @group Block
     */
    public function testGetYearWithNoDate(): void
    {
        self::assertSame('', $this->subject->getYear());
    }
}
