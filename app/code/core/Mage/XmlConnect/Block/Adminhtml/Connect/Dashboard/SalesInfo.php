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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin application sales info renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_SalesInfo extends Mage_Adminhtml_Block_Dashboard_Sales
{
    /**
     * Add sales info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_SalesInfo
     */
    public function addSalesInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        if (count($this->getTotals()) > 0) {
            /** @var $salesInfoField Mage_XmlConnect_Model_Simplexml_Form_Element_Custom */
            $salesInfoField = Mage::getModel('xmlconnect/simplexml_form_element_custom', array(
                'label' => ''
            ));
            $salesInfoField->setId('sales_info');
            $salesInfoXmlObj = $salesInfoField->toXmlObject();
            $storeId = null;
            foreach ($this->getTotals() as $total) {
                if (null === $storeId || $storeId !== $total['store_id']) {
                    $storeId = $total['store_id'];
                    $valuesXmlObj = $salesInfoXmlObj->addCustomChild('values', null, array('store_id' => $storeId));
                }
                $valuesXmlObj->addCustomChild('item', strip_tags($total['value']), array(
                    'label' => $total['label']
                ));
            }
            $xmlObj->appendChild($salesInfoXmlObj);
        }
        return $this;
    }

    /**
     * Prepare sales data collection
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_SalesInfo
     */
    protected function _prepareLayout()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }

        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $filter) {
            $this->setCurrentStore($filter);
            if ($filter) {
                Mage::app()->getStore($filter);
            }
            $collection = Mage::getResourceModel('reports/order_collection')
                ->calculateSales($filter);

            if ($filter) {
                $collection->addFieldToFilter('store_id', $filter);
            }

            $collection->load();
            $sales = $collection->getFirstItem();

            $this->addTotal($this->__('Lifetime Sales'), $sales->getLifetime());
            $this->addTotal($this->__('Average Orders'), $sales->getAverage());
        }
    }

    /**
     * Add totals data with current store id to totals array
     *
     * @param string $label
     * @param string $value
     * @param bool $isQuantity
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_SalesInfo
     */
    public function addTotal($label, $value, $isQuantity = false)
    {
        if (!$isQuantity) {
            $value = $this->format($value);
        }
        $this->_totals[] = array(
            'label' => $label,
            'value' => $value,
            'store_id' => $this->getCurrentStore() ? $this->getCurrentStore()
                : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS
        );

        return $this;
    }
}
