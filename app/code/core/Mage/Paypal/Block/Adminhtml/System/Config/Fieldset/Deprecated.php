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
 * Fieldset renderer for deprecated PayPal solutions
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Deprecated
    extends Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment
{
    /**
     * Get was enabled config path
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getWasActiveConfigPath(Varien_Data_Form_Element_Abstract $element)
    {
        $groupConfig = $this->getGroup($element)->asArray();
        return isset($groupConfig['was_enabled_path']) ? $groupConfig['was_enabled_path'] : '';
    }

    /**
     * Check whether solution was enabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _wasActive(Varien_Data_Form_Element_Abstract $element)
    {
        $wasActiveConfigPath = $this->_getWasActiveConfigPath($element);
        return empty($wasActiveConfigPath)
            ? false
            : (bool)(string)$this->_getConfigDataModel()->getConfigDataValue($wasActiveConfigPath);
    }

    /**
     * Set solution as was enabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Deprecated
     */
    protected function _setWasActive(Varien_Data_Form_Element_Abstract $element)
    {
        $wasActiveConfigPath = $this->_getWasActiveConfigPath($element);
        Mage::getConfig()->saveConfig($wasActiveConfigPath, 1);
        return $this;
    }

    /**
     * Get config data model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getConfigModel()
    {
        if (!$this->hasConfigModel()) {
            $this->setConfigModel(Mage::getConfig());
        }

        return $this->getConfigModel();
    }

    /**
     * Get all websites
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if (!$this->hasWebsites()) {
            $this->setWebsites(Mage::app()->getWebsites());
        }

        return $this->getWebsites();
    }

    /**
     * Check whether current payment method is enabled on any scope
     *
     * @param string $activityPath
     * @return bool
     */
    public function isPaymentEnabledAnyScope($activityPath)
    {
        if ((bool)(string)$this->_getConfigModel()->getNode($activityPath, 'default')) {
            return true;
        }
        foreach ($this->_getWebsites() as $website) {
            if ((bool)(string)$this->_getConfigModel()->getNode($activityPath, 'website', (int)$website->getId())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Do not render solution if disabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $isPaymentEnabled = $this->_isPaymentEnabled($element, array($this, 'isPaymentEnabledAnyScope'));
        if ($this->_wasActive($element) && $isPaymentEnabled) {
            return parent::render($element);
        }

        if ($isPaymentEnabled) {
            $this->_setWasActive($element);
            return parent::render($element);
        }

        return '';
    }
}
