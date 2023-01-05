<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create sidebar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'after_html' => '</div>'
            ]);
            $this->setChild('top_button', $button);
        }

        if ($this->getCustomerId()) {
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
