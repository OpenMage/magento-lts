<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Coupon;

use Mage_SalesRule_Model_Coupon_Massgenerator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Coupon\MassgeneratorTrait;

final class MassgeneratorTest extends OpenMageTest
{
    use MassgeneratorTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
