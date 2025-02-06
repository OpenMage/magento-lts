<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Field renderer for hidden fields
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_Hidden extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Decorate field row html to be invisible
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" style="display: none;">' . $html . '</tr>';
    }
}
