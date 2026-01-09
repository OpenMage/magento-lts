<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order history block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }

        $this->setOrder($this->getParentBlock()->getOrder());

        foreach ($this->getParentBlock()->getOrderInfoData() as $k => $v) {
            $this->setDataUsingMethod($k, $v);
        }

        return parent::_beforeToHtml();
    }

    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if (is_null($storeId)) {
                $deleted = Mage::helper('adminhtml')->__(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }

            $store = Mage::app()->getStore($storeId);
            $name = [
                $store->getWebsite()->getName(),
                $store->getGroup()->getName(),
                $store->getName(),
            ];
            return implode('<br/>', array_map([$this, 'escapeHtml'], $name));
        }

        return null;
    }

    public function getCustomerGroupName()
    {
        if ($this->getOrder()) {
            return Mage::getModel('customer/group')->load((int) $this->getOrder()->getCustomerGroupId())->getCode();
        }

        return null;
    }

    public function getCustomerViewUrl()
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('customer/manage')) {
            return false;
        }

        if ($this->getOrder()->getCustomerIsGuest() || !$this->getOrder()->getCustomerId()) {
            return false;
        }

        return $this->getUrl('*/customer/edit', ['id' => $this->getOrder()->getCustomerId()]);
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/sales_order/view', ['order_id' => $orderId]);
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param  int $sortOrder
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->_prepareAccountDataSortOrder($data, $sortOrder + 1);
        }

        return $sortOrder;
    }

    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     */
    public function getCustomerAccountData()
    {
        $accountData = [];

        /** @var Mage_Eav_Model_Config $config */
        $config     = Mage::getSingleton('eav/config');
        $entityType = 'customer';
        $customer   = Mage::getModel('customer/customer');
        foreach ($config->getEntityAttributeCodes($entityType) as $attributeCode) {
            /** @var Mage_Customer_Model_Attribute $attribute */
            $attribute = $config->getAttribute($entityType, $attributeCode);
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }

            $orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue != '') {
                $customer->setData($attribute->getAttributeCode(), $orderValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortOrder  = $attribute->getSortOrder() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortOrder  = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, ['br']),
                ];
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Get link to edit order address page
     *
     * @param  Mage_Sales_Model_Order_Address $address
     * @param  string                         $label
     * @return string
     */
    public function getAddressEditLink($address, $label = '')
    {
        if (empty($label)) {
            $label = $this->__('Edit');
        }

        $url = $this->getUrl('*/sales_order/address', ['address_id' => $address->getId()]);
        return '<a href="' . $url . '">' . $label . '</a>';
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !Mage::getStoreConfigFlag('sales/general/hide_customer_ip', $this->getOrder()->getStoreId());
    }
}
