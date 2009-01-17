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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create items grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Flag to check can items be move to customer storage
     *
     * @var bool
     */
    protected $_moveToCustomerStorage = true;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search_grid');
        $this->setTemplate('sales/order/create/items/grid.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('coupons',
            $this->getLayout()->createBlock('adminhtml/sales_order_create_coupons')
        );
        return parent::_prepareLayout();
    }

    public function getItems()
    {
        return $this->getParentBlock()->getItems();
    }

    public function getSession()
    {
        return $this->getParentBlock()->getSession();
    }

    public function getItemEditablePrice($item)
    {
        return $item->getCalculationPrice()*1;
    }

    public function getOriginalEditablePrice($item)
    {
        if ($item->hasOriginalCustomPrice()) {
            return $item->getOriginalCustomPrice()*1;
        } else {
            $result = $item->getCalculationPrice()*1;
            if (Mage::helper('tax')->priceIncludesTax($this->getStore()) && $item->getTaxPercent()) {
                $result = $result + ($result*($item->getTaxPercent()/100));
            }
            return $result;
        }
    }

    public function getItemOrigPrice($item)
    {
//        return $this->convertPrice($item->getProduct()->getPrice());
        return $this->convertPrice($item->getPrice());
    }

    public function isGiftMessagesAvailable($item=null)
    {
        if(is_null($item)) {
            return $this->helper('giftmessage/message')->getIsMessagesAvailable(
                'main', $this->getQuote(), $this->getStore()
            );
        }

        return $this->helper('giftmessage/message')->getIsMessagesAvailable(
            'item', $item, $this->getStore()
        );
    }

    public function isAllowedForGiftMessage($item)
    {
        return Mage::getSingleton('adminhtml/giftmessage_save')->getIsAllowedQuoteItem($item);
    }

    public function getSubtotal()
    {
        $totals = $this->getQuote()->getTotals();
        if (isset($totals['subtotal'])) {
            return $totals['subtotal']->getValue();
        }
        return false;
    }

    public function getSubtotalWithDiscount()
    {
        return $this->getQuote()->getShippingAddress()->getSubtotalWithDiscount();
    }

    public function getDiscountAmount()
    {
        return $this->getQuote()->getShippingAddress()->getDiscountAmount();
    }

    public function usedCustomPriceForItem($item)
    {
        return $item->hasCustomPrice();
    }

    public function getQtyTitle($item)
    {
        if ($prices = $item->getProduct()->getTierPrice()) {
            $info = array();
            foreach ($prices as $data) {
                $qty    = $data['price_qty']*1;
                $price  = $this->convertPrice($data['price']);
            	$info[] = $this->helper('sales')->__('Buy %s for price %s', $qty, $price);
            }
            return implode(', ', $info);
        }
        else {
            return $this->helper('sales')->__('Item ordered qty');
        }
    }

    public function getTierHtml($item)
    {
        $html = '';
        if ($prices = $item->getProduct()->getTierPrice()) {
            foreach ($prices as $data) {
                $qty    = $data['price_qty']*1;
                $price  = $this->convertPrice($data['price']);
            	$info[] = $this->helper('sales')->__('%s for %s', $qty, $price);
            }
            $html = implode('<br/>', $info);
        }
        return $html;
    }

    /**
     * Get Custom Options of item
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getCustomOptions(Mage_Sales_Model_Quote_Item $item)
    {
        $optionStr = '';
        $this->_moveToCustomerStorage = true;
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $item->getProduct()->getOptionById($optionId)) {
                    $optionValue = $item->getOptionByCode('option_' . $option->getId())->getValue();
                    $optionStr .= $option->getTitle() . ':';
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        foreach (explode(',', $optionValue) as $_value) {
                            $optionStr .= $option->getValueById($_value)->getTitle() . ', ';
                        }
                        $optionStr = Mage::helper('core/string')->substr($optionStr, 0, -2);
                    } elseif ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $optionStr .= $option->getValueById($optionValue)->getTitle();
                    } else {
                        $optionStr .= $optionValue;
                    }
                    $optionStr .= "\n";
                }
            }
        }
        foreach ($item->getProduct()->getOptions() as $option) {
            if ($option->getIsRequire() && !$item->getOptionByCode('option_'.$option->getId())) {
                $optionStr .= $option->getTitle() . ':' . "\n";
            }
        }
        if ($additionalOptions = $item->getOptionByCode('additional_options')) {
            $this->_moveToCustomerStorage = false;
            foreach (unserialize($additionalOptions->getValue()) as $additionalOption) {
                $optionStr .= $additionalOption['label'] . ':' . $additionalOption['value'] . "\n";
            }
        }
        $optionStr = $this->helper('core/string')->substr($optionStr, 0, -1);

        return $optionStr;
    }

    /**
     * Get flag for rights to move items to customer storage
     *
     * @return bool
     */
    public function getMoveToCustomerStorage()
    {
        return $this->_moveToCustomerStorage;
    }

    public function displaySubtotalInclTax($item)
    {
        $tax = ($item->getTaxBeforeDiscount() ? $item->getTaxBeforeDiscount() : ($item->getTaxAmount() ? $item->getTaxAmount() : 0));
        return $this->formatPrice($item->getRowTotal()+$tax);
    }

    public function displayOriginalPriceInclTax($item)
    {
        $tax = 0;
        if ($item->getTaxPercent()) {
            $tax = $item->getPrice()*($item->getTaxPercent()/100);
        }
        return $this->convertPrice($item->getPrice()+($tax/$item->getQty()));
    }

    public function displayRowTotalWithDiscountInclTax($item)
    {
        $tax = ($item->getTaxAmount() ? $item->getTaxAmount() : 0);
        return $this->formatPrice($item->getRowTotalWithDiscount()+$tax);
    }

    public function getInclExclTaxMessage()
    {
        if (Mage::helper('tax')->priceIncludesTax($this->getStore())) {
            return Mage::helper('sales')->__('* - Enter custom price including tax');
        } else {
            return Mage::helper('sales')->__('* - Enter custom price excluding tax');
        }
    }
}