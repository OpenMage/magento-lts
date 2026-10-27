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

trait ExceptionTrait
{
    /**
     * @return Iterator<string, array{bool, string, array<string, mixed>}>
     */
    public static function provideIsPayerActionRequiredData(): Iterator
    {
        yield 'issue named in the message' => [true, 'UNPROCESSABLE_ENTITY: PAYER_ACTION_REQUIRED', []];
        yield 'issue in the error details' => [true, 'The requested action could not be performed.', ['details' => [['issue' => 'PAYER_ACTION_REQUIRED']]]];
        yield 'order status awaiting the buyer' => [true, 'Please approve the updated order total.', ['status' => 'PAYER_ACTION_REQUIRED']];
        yield 'other error' => [false, 'The requested action could not be performed.', ['details' => [['issue' => 'INSTRUMENT_DECLINED']]]];
    }
}
