<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Hosted Sole Solution helper
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Helper_Hss extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Paypal';

    /**
     * Hosted Sole Solution methods
     *
     * @var array
     */
    protected $_hssMethods = [
        Mage_Paypal_Model_Config::METHOD_HOSTEDPRO,
        Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK,
        Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED,
    ];

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_hssMethods)) {
                return $name;
            }
        }

        if ($blockObject = Mage::getSingleton('core/layout')->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    /**
     * Get methods
     *
     * @return array
     */
    public function getHssMethods()
    {
        return $this->_hssMethods;
    }
}
