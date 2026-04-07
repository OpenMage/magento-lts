<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Recurring;

// use Mage_Sales_Block_Recurring_Profiles as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Recurring\ProfilesTrait;

final class ProfilesTest extends OpenMageTest
{
    use ProfilesTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
