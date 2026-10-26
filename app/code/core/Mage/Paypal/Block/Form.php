<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal payment form block
 */
class Mage_Paypal_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Initializes the block by setting the payment template.
     */
    #[Override]
    protected function _construct(): void
    {
        parent::_construct();
        $this->setTemplate('paypal/payment.phtml');
    }

    /**
     * Settings for rendering the PayPal SDK v6 buttons at checkout.
     *
     * @return array<string, bool|string>
     */
    public function getSdkConfig(): array
    {
        return Mage::helper('paypal')->getConfig()->getSdkConfig(
            Mage_Paypal_Model_Config::PAGE_TYPE_CHECKOUT,
            $this->getCurrencyCode(),
            $this->getAmount(),
        );
    }

    /**
     * Retrieves the grand total amount from the current quote.
     */
    public function getAmount(): float
    {
        return (float) $this->getMethod()->getQuote()->getGrandTotal();
    }

    /**
     * Retrieves the currency code for the current store.
     */
    public function getCurrencyCode(): string
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
}
