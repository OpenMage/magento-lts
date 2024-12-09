<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create sidebar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public const BUTTON_BOTTOM  = 'bottom_button';
    public const BUTTON_TOP     = 'top_button';

    protected function _prepareLayout()
    {
        if ($this->getCustomerId()) {
            $button = $this->getButtonTopBlock();
            $this->setChild(self::BUTTON_TOP, $button);

            $button = clone $button;
            $button->unsId();
            $this->setChild(self::BUTTON_BOTTOM, $button);
        }

        return parent::_prepareLayout();
    }

    public function getButtonTopBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock()
            ->setLabel(Mage::helper('sales')->__('Update Changes'))
            ->setOnClick('order.sidebarApplyChanges()')
            ->setBeforeHtml('<div class="sub-btn-set">')
            ->setAfterHtml('</div>');
    }

    public function canDisplay($child)
    {
        if (method_exists($child, 'canDisplay')) {
            return $child->canDisplay();
        }
        return true;
    }
}
