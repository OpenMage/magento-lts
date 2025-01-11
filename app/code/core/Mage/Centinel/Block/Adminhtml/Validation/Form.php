<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create validation card block
 *
 * @category   Mage
 * @package    Mage_Centinel
 */
class Mage_Centinel_Block_Adminhtml_Validation_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Prepare validation and template parameters
     */
    protected function _toHtml()
    {
        $payment = $this->getQuote()->getPayment();
        if ($payment && $method = $payment->getMethodInstance()) {
            if ($method->getIsCentinelValidationEnabled() && $centinel = $method->getCentinelValidator()) {
                $this->setFrameUrl($centinel->getValidatePaymentDataUrl())
                    ->setContainerId('centinel_authenticate_iframe')
                    ->setMethodCode($method->getCode())
                ;
                return parent::_toHtml();
            }
        }
        return '';
    }
}
