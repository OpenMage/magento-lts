<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
