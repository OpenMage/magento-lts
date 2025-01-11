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
 * Adminhtml sales order create items block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Contains button descriptions to be shown at the top of accordion
     * @var array
     */
    protected $_buttons = [];

    /**
     * Define block ID
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_items');
    }

    /**
     * Accordion header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Items Ordered');
    }

    /**
     * Returns all visible items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Add button to the items header
     *
     * @param array $args
     */
    public function addButton($args)
    {
        $this->_buttons[] = $args;
    }

    /**
     * Render buttons and return HTML code
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = '';
        // Make buttons to be rendered in opposite order of addition. This makes "Add products" the last one.
        $this->_buttons = array_reverse($this->_buttons);
        foreach ($this->_buttons as $buttonData) {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData($buttonData)->toHtml();
        }

        return $html;
    }

    /**
     * Return HTML code of the block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getStoreId()) {
            return parent::_toHtml();
        }
        return '';
    }
}
