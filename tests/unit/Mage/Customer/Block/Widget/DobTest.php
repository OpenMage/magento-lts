<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Block\Widget;

use Override;
use Mage_Customer_Block_Widget_Dob as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Block\Widget\DobTrait;

final class DobTest extends OpenMageTest
{
    use DobTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     * @dataProvider provideGetYearData
     */
    public function testGetYear(string $expectedYear, string $date): void
    {
        self::$subject->setDate($date);
        self::assertSame($expectedYear, self::$subject->getYear());
    }
}
