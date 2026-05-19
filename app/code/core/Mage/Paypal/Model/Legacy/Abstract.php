<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * Read-only payment method for historical PayPal orders.
 */
abstract class Mage_Paypal_Model_Legacy_Abstract extends Mage_Payment_Model_Method_Abstract
{
    private const INFO_BLOCK_TYPE = 'paypal/adminhtml_info';

    protected const METHOD_CODE = '';

    #[Override]
    public function getCode(): string
    {
        if (static::METHOD_CODE === '') {
            Mage::throwException(Mage::helper('payment')->__('Cannot retrieve the payment method code.'));
        }

        return static::METHOD_CODE;
    }

    #[Override]
    public function getInfoBlockType(): string
    {
        return self::INFO_BLOCK_TYPE;
    }

    #[Override]
    public function canUseCheckout(): bool
    {
        return false;
    }

    #[Override]
    public function canUseInternal(): bool
    {
        return false;
    }

    #[Override]
    public function canUseForMultishipping(): bool
    {
        return false;
    }

    #[Override]
    public function isAvailable($quote = null): bool
    {
        return false;
    }

    #[Override]
    public function getConfigPaymentAction(): string
    {
        return '';
    }
}
