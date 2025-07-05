<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Dashboard search query column renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Searches_Renderer_Searchquery extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (Mage::helper('core/string')->strlen($value) > 30) {
            $value = '<span title="' . $this->escapeHtml($value) . '">'
                . $this->escapeHtml(Mage::helper('core/string')->truncate($value, 30)) . '</span>';
        } else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
