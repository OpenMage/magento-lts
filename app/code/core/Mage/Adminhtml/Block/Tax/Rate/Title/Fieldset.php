<?php
/**
 * Tax Rate Titles Fieldset
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
class Mage_Adminhtml_Block_Tax_Rate_Title_Fieldset extends Varien_Data_Form_Element_Fieldset
{
    public function getChildrenHtml()
    {
        return Mage::getBlockSingleton('adminhtml/tax_rate_title')->toHtml();
    }
}
