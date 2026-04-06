<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GoogleCheckout\Model;

use Mage;
use Mage_GoogleCheckout_Model_Payment as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GoogleCheckout\Model\PaymentTrait;

final class PaymentTest extends OpenMageTest
{
    use PaymentTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('googlecheckout/payment');
        self::markTestSkipped('');
    }
}
