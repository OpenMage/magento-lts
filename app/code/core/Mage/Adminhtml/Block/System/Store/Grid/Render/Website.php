<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Store render website
 *
 * @package    Mage_Adminhtml
 * @deprecated after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree
 */
class Mage_Adminhtml_Block_System_Store_Grid_Render_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @return string
     */
    public function render(Varien_Object $row)
    {
        return '<a title="' . Mage::helper('core')->__('Edit Website') . '"
            href="' . $this->getUrl('*/*/editWebsite', ['website_id' => $row->getWebsiteId()]) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }
}
