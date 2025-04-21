<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Dashboard Month-To-Date Day starts Field Renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Config_Form_Field_MtdStart extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $_days = [];
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0' . $i : $i;
        }

        return $element->setStyle('width:50px;')
            ->setValues($_days)
            ->getElementHtml();
    }
}
