<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Store render group
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @deprecated after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree
 */
class Mage_Adminhtml_Block_System_Store_Grid_Render_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @return string|null
     */
    public function render(Varien_Object $row)
    {
        if (!$row->getData($this->getColumn()->getIndex())) {
            return null;
        }
        return '<a title="' . Mage::helper('core')->__('Edit Store') . '"
            href="' . $this->getUrl('*/*/editGroup', ['group_id' => $row->getGroupId()]) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }
}
