<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Checkout\Model\Type;

use Generator;
use Mage_Checkout_Model_Type_Onepage;
use Mage_Sales_Model_Quote;

trait OnepageTrait
{
    /**
     * @return Generator<string, array{array<string, int|string>, string}>
     */
    public static function provideQuoteData(): Generator
    {
        yield 'customer quote' => [
            ['customer_id' => 1],
            Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN,
        ];
        yield 'register' => [
            ['checkout_method' => Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER],
            Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER,
        ];
        yield 'guest' => [
            ['checkout_method' => Mage_Checkout_Model_Type_Onepage::METHOD_GUEST],
            Mage_Checkout_Model_Type_Onepage::METHOD_GUEST,
        ];
    }
}
