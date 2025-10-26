<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Backend model for base currency
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Block_Adminhtml_Frontend_Currency_Base extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @inheritDoc
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->getRequest()->getParam('website') != '') {
            $priceScope = Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            if ($priceScope == Mage_Core_Model_Store::PRICE_SCOPE_GLOBAL) {
                return '';
            }
        }

        return parent::render($element);
    }
}
