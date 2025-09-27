<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter templates grid block sender item renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Sender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $str = '';
        if ($row->getTemplateSenderName()) {
            $str .= $this->escapeHtml($row->getTemplateSenderName()) . ' ';
        }
        if ($row->getTemplateSenderEmail()) {
            $str .= '[' . $this->escapeHtml($row->getTemplateSenderEmail()) . ']';
        }
        if ($str == '') {
            $str .= '---';
        }
        return $str;
    }
}
