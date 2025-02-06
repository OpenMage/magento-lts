<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml system templates grid block type item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $_types = [
        Mage_Core_Model_Template::TYPE_HTML => 'HTML',
        Mage_Core_Model_Template::TYPE_TEXT => 'Text',
    ];

    /**
     * @return string
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function render(Varien_Object $row)
    {
        $str = self::$_types[$row->getTemplateType()] ?? Mage::helper('adminhtml')->__('Unknown');
        return Mage::helper('adminhtml')->__($str);
    }
}
