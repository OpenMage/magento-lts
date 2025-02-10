<?php
/**
 * Adminhtml sales order create sidebar
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected function _prepareLayout()
    {
        if ($this->getCustomerId()) {
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label' => Mage::helper('sales')->__('Update Changes'),
                'onclick' => 'order.sidebarApplyChanges()',
                'before_html' => '<div class="sub-btn-set">',
                'after_html' => '</div>',
            ]);
            $this->setChild('top_button', $button);

            $button = clone $button;
            $button->unsId();
            $this->setChild('bottom_button', $button);
        }

        return parent::_prepareLayout();
    }

    public function canDisplay($child)
    {
        if (method_exists($child, 'canDisplay')) {
            return $child->canDisplay();
        }
        return true;
    }
}
