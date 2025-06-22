<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal payment information block for frontend order view
 */
class Mage_Paypal_Block_Info extends Mage_Payment_Block_Info
{
    /**
     * Initializes the block by setting the payment template.
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setTemplate('paypal/info.phtml');
    }

    /**
     * Retrieves the transaction ID from the payment information.
     */
    public function getTransactionId(): ?string
    {
        return $this->getInfo()->getLastTransId();
    }
}
