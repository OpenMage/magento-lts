<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Fieldset renderer for PayPal solutions which have dependencies on other solutions
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_PathDependent extends Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment
{
    /**
     * Check whether current payment method has active dependencies
     *
     * @param  array $groupConfig
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
