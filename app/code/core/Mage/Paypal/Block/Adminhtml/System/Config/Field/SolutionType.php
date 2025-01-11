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
 * Field renderer for hidden fields
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_SolutionType extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $countryCode = Mage::helper('paypal')->getConfigurationCountryCode();
        if ($countryCode === 'DE') {
            /** @var Mage_Paypal_Block_Adminhtml_System_Config_Field_Hidden $block */
            $block = Mage::getBlockSingleton('paypal/adminhtml_System_config_field_hidden');
            return $block->render($element);
        }

        return parent::render($element);
    }
}
