<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fieldset renderer for PayPal solutions which have dependencies on other solutions
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_PathDependent
    extends Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment
{
    /**
     * Check whether current payment method has active dependencies
     *
     * @param array $groupConfig
     * @return bool
     */
    public function hasActivePathDependencies($groupConfig)
    {
        $activityPath = isset($groupConfig['hide_case_path']) ? $groupConfig['hide_case_path'] : '';
        return !empty($activityPath) && (bool)(string)$this->_getConfigDataModel()->getConfigDataValue($activityPath);
    }

    /**
     * Do not render solution if disabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
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
