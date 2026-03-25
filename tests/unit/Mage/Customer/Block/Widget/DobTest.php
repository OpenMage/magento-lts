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
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Block\Widget\DobTrait;

final class DobTest extends OpenMageTest
{
    use DobTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
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
        $subject = new Subject();
        $subject->setDate($date);
        self::assertSame($expectedYear, $subject->getYear());
    }

    /**
     * @group Block
     */
    public function testGetYearWithNoDate(): void
    {
        self::assertSame('', self::$subject->getYear());
    }
}
