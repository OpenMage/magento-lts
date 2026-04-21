<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payflow link infoblock
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Link_Info extends Mage_Paypal_Block_Payment_Info
{
    /**
     * Don't show CC type
     *
     * @return false
     */
    #[Override]
    public function getCcTypeName()
    {
        return false;
    }
}
