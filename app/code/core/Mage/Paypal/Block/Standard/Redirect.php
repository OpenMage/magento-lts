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
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $standard = Mage::getModel('paypal/standard');

        $form = new Varien_Data_Form();
        $form->setAction($standard->getConfig()->getPaypalUrl())
            ->setId('paypal_standard_checkout')
            ->setName('paypal_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', ['name' => $field, 'value' => $value]);
        }
        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit([
            'value'    => $this->__('Click here if you are not redirected within 10 seconds...'),
        ]);
        $id = "submit_to_paypal_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);
        $html = '<html><body>';
        $html .= $this->__('You will be redirected to the PayPal website in a few seconds.');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.getElementById("paypal_standard_checkout").submit();</script>';
        $html .= '</body></html>';

        return $html;
    }
}
