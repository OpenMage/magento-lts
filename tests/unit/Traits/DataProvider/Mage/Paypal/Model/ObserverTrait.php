<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model;

use Iterator;
use Mage_Paypal_Model_Payment;
use Mage_Sales_Model_Order_Payment_Transaction;

trait ObserverTrait
{
    /**
     * @return Iterator<string, array{string, array<string, mixed>}>
     */
    public static function provideSubmittableQuotes(): Iterator
    {
        yield 'another payment method' => ['checkmo', []];
        yield 'PayPal capture recorded' => ['paypal', [Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => ['capture_id' => 'CAP-1']]];
        yield 'PayPal authorization recorded' => ['paypal', [Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => ['authorization_id' => 'AUTH-1']]];
    }

    /**
     * @return Iterator<string, array{array<string, mixed>}>
     */
    public static function provideUnpaidPaypalQuotes(): Iterator
    {
        yield 'nothing recorded' => [[]];
        yield 'PayPal order created but never charged' => [[Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID => 'REQ-1']];
        yield 'raw details without a charge' => [[Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => ['id' => '5O190127TN364715T']]];
    }
}
