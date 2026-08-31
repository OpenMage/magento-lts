<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Tax Rate Titles Fieldset
 *
 * @package    Mage_Adminhtml
 *
 * @method string getLegend()
 * @method $this  setLegend(string $value)
 * @deprecated Use a regular fieldset and set Mage_Adminhtml_Block_Tax_Rate_Title as the element renderer (adminhtml/tax_rate_title)
 */
class Mage_Adminhtml_Block_Tax_Rate_Title_Fieldset extends Varien_Data_Form_Element_Fieldset
{
    #[Override]
    public function getChildrenHtml()
    {
        return Mage::getBlockSingleton('adminhtml/tax_rate_title')->toHtml();
    }
}
