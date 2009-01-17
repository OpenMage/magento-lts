<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract items renderer
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Items_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * Renderers with render type key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_itemRenders = array();

    /**
     * Renderers for other column with column name key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_columnRenders = array();

    /**
     * Init block
     *
     */
    protected function _construct()
    {
        $this->addColumnRender('qty', 'adminhtml/sales_items_column_qty', 'sales/items/column/qty.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name', 'sales/items/column/name.phtml');
        parent::_construct();
    }

    /**
     * Add item renderer
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Mage_Adminhtml_Block_Sales_Items_Abstract
     */
    public function addItemRender($type, $block, $template)
    {
        $this->_itemRenders[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }

    /**
     * Add column renderer
     *
     * @param string $column
     * @param string $block
     * @param string $template
     * @return Mage_Adminhtml_Block_Sales_Items_Abstract
     */
    public function addColumnRender($column, $block, $template)
    {
        $this->_columnRenders[$column] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $this->_itemRenders[$type]['renderer'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                ->setTemplate($this->_itemRenders[$type]['template']);
        }
        return $this->_itemRenders[$type]['renderer'];
    }

    /**
     * Retrieve column renderer block
     *
     * @param string $column
     * @return Mage_Core_Block_Abstract
     */
    public function getColumnRenderer($column)
    {
        if (!isset($this->_columnRenders[$column])) {
            return false;
        }
        if (is_null($this->_columnRenders[$column]['renderer'])) {
            $this->_columnRenders[$column]['renderer'] = $this->getLayout()
                ->createBlock($this->_columnRenders[$column]['block'])
                ->setTemplate($this->_columnRenders[$column]['template']);
        }
        return $this->_columnRenders[$column]['renderer'];
    }

    /**
     * Retrieve rendered item html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getItemHtml(Varien_Object $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }

        return $this->getItemRenderer($type)
            ->setItem($item)
            ->toHtml();
    }

    /**
     * Retrieve rendered column html content
     *
     * @param Varien_Object $item
     * @param string $column the column key
     * @param string $field the custom item field
     * @return string
     */
    public function getColumnHtml(Varien_Object $item, $column, $field = null)
    {
        if ($block = $this->getColumnRenderer($column)) {
            $block->setItem($item);
            if (!is_null($field)) {
                $block->setField($field);
            }
            return $block->toHtml();
        }
        return '&nbsp;';
    }

    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * ######################### SALES ##################################
     */

    /**
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        if ($this->getInvoice())
        {
            return $this->getInvoice()->getOrder();
        }
        if ($this->getCreditmemo())
        {
            return $this->getCreditmemo()->getOrder();
        }
        if ($this->getItem()->getOrder())
        {
            return $this->getItem()->getOrder();
        }

        Mage::throwException(Mage::helper('sales')->__('Can\'t get order instance'));
    }

    /**
     * Retrieve price data object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if (is_null($obj)) {
            return $this->getOrder();
        }
        return $obj;
    }

    /**
     * Retrieve price attribute html content
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        return $this->displayPrices(
            $this->getPriceDataObject()->getData('base_'.$code),
            $this->getPriceDataObject()->getData($code),
            $strong,
            $separator
        );
    }

    /**
     * Retrieve price formated html content
     *
     * @param float $basePrice
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />')
    {
        if ($this->getOrder()->isCurrencyDifferent()) {
            $res = '';
            $res.= $this->getOrder()->formatBasePrice($basePrice);
            $res.= $separator;
            $res.= $this->getOrder()->formatPrice($price, true);
        }
        else {
            $res = $this->getOrder()->formatPrice($price);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        }
        return $res;
    }

    /**
     * Retrieve include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayPriceInclTax(Varien_Object $item)
    {
        $qty = ($item->getQtyOrdered() ? $item->getQtyOrdered() : ($item->getQty() ? $item->getQty() : 1));
        $baseTax = ($item->getTaxBeforeDiscount() ? $item->getTaxBeforeDiscount() : ($item->getTaxAmount() ? $item->getTaxAmount() : 0));
        $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : ($item->getBaseTaxAmount() ? $item->getBaseTaxAmount() : 0));

        return $this->displayPrices(
            $this->getOrder()->getStore()->roundPrice($item->getBasePrice()+$baseTax/$qty),
            $this->getOrder()->getStore()->roundPrice($item->getPrice()+$tax/$qty)
        );
    }

    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        $baseTax = ($item->getTaxBeforeDiscount() ? $item->getTaxBeforeDiscount() : ($item->getTaxAmount() ? $item->getTaxAmount() : 0));
        $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : ($item->getBaseTaxAmount() ? $item->getBaseTaxAmount() : 0));

        return $this->displayPrices(
            $item->getBaseRowTotal()+$baseTax,
            $item->getRowTotal()+$tax
        );
    }

    /**
     * Retrieve tax calculation html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayTaxCalculation(Varien_Object $item)
    {
        if ($item->getTaxPercent() && $item->getTaxString() == '') {
            $percents = array($item->getTaxPercent());
        } else if ($item->getTaxString()) {
            $percents = explode(Mage_Tax_Model_Config::CALCULATION_STRING_SEPARATOR, $item->getTaxString());
        } else {
            return '0%';
        }

        foreach ($percents as &$percent) {
            $percent = sprintf('%.2f%%', $percent);
        }
        return implode(' + ', $percents);
    }

    /**
     * Retrieve tax with persent html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayTaxPercent(Varien_Object $item)
    {
        if ($item->getTaxPercent()) {
            return sprintf('%.2f%%', $item->getTaxPercent());
        } else {
            return '0%';
        }
    }

    /**
     *  INVOICES
     */

    /**
     * Check shipment availability for current invoice
     *
     * @return bool
     */
    public function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->getQtyToShip()) {
                return true;
            }
        }
        return false;
    }

    public function canEditQty()
    {
        if ($this->getOrder()->getPayment()->canCapture()) {
            return $this->getOrder()->getPayment()->canCapturePartial();
        }
        return true;
    }

    public function canCapture()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/capture')) {
            return $this->getInvoice()->canCapture();
        }
        return false;
    }

    public function formatPrice($price)
    {
        return $this->getOrder()->formatPrice($price);
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * CREDITMEMO
     */

    public function canReturnToStock() {

        $canReturnToStock = Mage::getStoreConfig('cataloginventory/options/can_subtract');
        if (Mage::getStoreConfig('cataloginventory/options/can_subtract')) {
            return true;
        } else {
            return false;
        }
    }

    public function canShipPartially()
    {
        $value = Mage::registry('current_shipment')->getOrder()->getCanShipPartially();
        if (!is_null($value) && !$value) {
            return false;
        }
        return true;
    }

    public function canShipPartiallyItem()
    {
        $value = Mage::registry('current_shipment')->getOrder()->getCanShipPartiallyItem();
        if (!is_null($value) && !$value) {
            return false;
        }
        return true;
    }

    public function isShipmentRegular()
    {
        if (!$this->canShipPartiallyItem() || !$this->canShipPartially()) {
            return false;
        }
        return true;
    }

}