<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Fieldset renderer for PayPal solutions group
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Group extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Return header comment part of html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        $groupConfig = $this->getGroup($element)->asArray();

        if (empty($groupConfig['help_url']) || !$element->getComment()) {
            return parent::_getHeaderCommentHtml($element);
        }

        return '<div class="comment">' . $element->getComment()
            . ' <a target="_blank" href="' . $groupConfig['help_url'] . '">'
            . Mage::helper('paypal')->__('Help') . '</a></div>';
    }

    /**
     * Return collapse state
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return int|false
     */
    protected function _getCollapseState($element)
    {
        $extra = Mage::getSingleton('admin/session')->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        if ($element->getExpanded() !== null) {
            return 1;
        }

        return false;
    }
}
