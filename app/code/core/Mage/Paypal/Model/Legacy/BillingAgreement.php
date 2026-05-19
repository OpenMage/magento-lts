<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Legacy_BillingAgreement extends Mage_Paypal_Model_Legacy_Abstract
{
    protected const INFO_BLOCK_TYPE = 'sales/payment_info_billing_agreement';

    protected const METHOD_CODE = 'paypal_billing_agreement';
}
