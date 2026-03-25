<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Field renderer for hidden fields
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_Hidden extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Decorate field row html to be invisible
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @param  string                            $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" style="display: none;">' . $html . '</tr>';
    }
}
