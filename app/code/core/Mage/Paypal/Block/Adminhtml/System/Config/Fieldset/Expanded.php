<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fielset renderer which expanded by default
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Expanded
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
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
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }
}
