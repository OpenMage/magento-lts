<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * Display-only legacy Hosted Pro payment information block.
 */
class Mage_Paypal_Block_Hosted_Pro_Info extends Mage_Paypal_Block_Payment_Info
{
    /**
     * Don't show CC type.
     */
    #[Override]
    public function getCcTypeName(): string
    {
        return '';
    }
}
