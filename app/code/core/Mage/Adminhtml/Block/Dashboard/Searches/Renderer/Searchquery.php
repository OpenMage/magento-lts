<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Dashboard search query column renderer
 *
 * @category   Mage
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
