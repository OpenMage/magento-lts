<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Fielset renderer which expanded by default
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Expanded extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Return collapse state
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _getCollapseState($element)
    {
        $extra = Mage::getSingleton('admin/session')->getUser()->getExtra();
        return $extra['configState'][$element->getId()] ?? true;
    }
}
