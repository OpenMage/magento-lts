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
 * Fieldset renderer for PayPal solutions which have dependencies on other solutions
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_PathDependent extends Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment
{
    /**
     * Check whether current payment method has active dependencies
     *
     * @param array $groupConfig
     * @return bool
     */
    public function hasActivePathDependencies($groupConfig)
    {
        $activityPath = $groupConfig['hide_case_path'] ?? '';
        return !empty($activityPath) && (bool) (string) $this->_getConfigDataModel()->getConfigDataValue($activityPath);
    }

    /**
     * Do not render solution if disabled
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (!$this->hasActivePathDependencies($this->getGroup($element)->asArray())) {
            return parent::render($element);
        }

        return '';
    }
}
