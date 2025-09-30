<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Cash on delivery payment method model
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Method_Cashondelivery extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code  = 'cashondelivery';

    /**
     * Cash On Delivery payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'payment/form_cashondelivery';
    protected $_infoBlockType = 'payment/info';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    /**
     * Not available for quote without delivery
     *
     * {@inheritDoc}
     */
    public function isApplicableToQuote($quote, $checksBitMask)
    {
        if ($quote->getIsVirtual()) {
            return false;
        }
        return parent::isApplicableToQuote($quote, $checksBitMask);
    }
}
