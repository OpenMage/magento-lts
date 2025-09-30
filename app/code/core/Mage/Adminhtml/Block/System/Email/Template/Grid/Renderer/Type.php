<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml system templates grid block type item renderer
 *
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
