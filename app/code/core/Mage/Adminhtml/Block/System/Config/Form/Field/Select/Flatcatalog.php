<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System configuration shipping methods allow all countries select
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Select_Flatcatalog extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if (!Mage::helper('catalog/category_flat')->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }

        return parent::_getElementHtml($element);
    }
}
