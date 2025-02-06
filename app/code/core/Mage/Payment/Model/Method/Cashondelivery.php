<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * Cash on delivery payment method model
 *
 * @category   Mage
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
