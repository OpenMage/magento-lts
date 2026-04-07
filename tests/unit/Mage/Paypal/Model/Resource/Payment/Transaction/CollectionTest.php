<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Resource\Payment\Transaction;

// use Mage;
// use Mage_Paypal_Model_Resource_Payment_Transaction_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Resource\Payment\Transaction\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('paypal/resource_payment_transaction_collection');
        self::markTestSkipped('');
    }
}
