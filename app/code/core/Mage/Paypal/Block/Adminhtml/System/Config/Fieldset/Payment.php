<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Fieldset renderer for PayPal solution
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Add custom css class
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button '
            . ($this->_isPaymentEnabled($element) ? ' enabled' : '');
    }

    /**
     * Check whether current payment method is enabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param null|callable $configCallback
     * @return bool
     */
    protected function _isPaymentEnabled($element, $configCallback = null)
    {
        $groupConfig = $this->getGroup($element)->asArray();
        $activityPath = $groupConfig['activity_path'] ?? '';

        if (empty($activityPath)) {
            return false;
        }

        if ($configCallback && is_callable($configCallback)) {
            $isPaymentEnabled = call_user_func($configCallback, $activityPath);
        } else {
            $isPaymentEnabled = (bool) (string) $this->_getConfigDataModel()->getConfigDataValue($activityPath);
        }

        return (bool) $isPaymentEnabled;
    }

    /**
     * Get config data model
     *
     * @return Mage_Adminhtml_Model_Config_Data
     */
    protected function _getConfigDataModel()
    {
        if (!$this->hasConfigDataModel()) {
            $this->setConfigDataModel(Mage::getSingleton('adminhtml/config_data'));
        }

        return $this->getConfigDataModel();
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading" ><div class="heading"><strong>' . $element->getLegend();

        $groupConfig = $this->getGroup($element)->asArray();
        if (!empty($groupConfig['learn_more_link'])) {
            $html .= '<a class="link-more" href="' . $groupConfig['learn_more_link'] . '" target="_blank">'
                . $this->__('Learn More') . '</a>';
        }

        if (!empty($groupConfig['demo_link'])) {
            $html .= '<a class="link-demo" href="' . $groupConfig['demo_link'] . '" target="_blank">'
                . $this->__('View Demo') . '</a>';
        }

        $html .= '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }

        $html .= '</div>';

        return $html . ('<div class="button-container"><button type="button"'
            . ($this->_isPaymentEnabled($element) ? '' : ' disabled="disabled"') . ' class="button'
            . (empty($groupConfig['paypal_ec_separate']) ? '' : ' paypal-ec-separate')
            . ($this->_isPaymentEnabled($element) ? '' : ' disabled') . '" id="' . $element->getHtmlId()
            . '-head" onclick="paypalToggleSolution.call(this, \'' . $element->getHtmlId() . "', '"
            . $this->getUrl('*/*/state') . '\'); return false;"><span class="state-closed">'
            . $this->__('Configure') . '</span><span class="state-opened">'
            . $this->__('Close') . '</span></button></div></div>');
    }

    /**
     * Return header comment part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _getCollapseState($element)
    {
        return false;
    }
}
