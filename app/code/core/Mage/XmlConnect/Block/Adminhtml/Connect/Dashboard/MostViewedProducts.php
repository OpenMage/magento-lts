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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Most viewed products xml renderer block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_MostViewedProducts
    extends Mage_Adminhtml_Block_Dashboard_Tab_Products_Viewed
{
    /**
     * Products count to display
     */
    const PRODUCTS_COUNT_LIMIT = 5;

    /**
     * Get rid of unnecessary collection initialization
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_MostViewedProducts
     */
    protected function _prepareCollection()
    {
        return $this;
    }

    /**
     * Init last search terms collection
     *
     * @param int|null $storeId
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_MostViewedProducts
     */
    protected function _initCollection($storeId)
    {
        $collection = Mage::getResourceModel('reports/product_collection')->addAttributeToSelect('*')->addViewsCount()
            ->setStoreId($storeId)->addStoreFilter($storeId)->setPageSize(self::PRODUCTS_COUNT_LIMIT);
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Clear collection
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_MostViewedProducts
     */
    protected function _clearCollection()
    {
        $this->_collection = null;
        return $this;
    }

    /**
     * Add most viewed products to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_MostViewedProducts
     */
    public function addMostViewedProductsToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $storeId) {
            $this->_clearCollection()->_initCollection($storeId);
            $valuesXml = $xmlObj->addCustomChild('values', null, array(
                'store_id' => $storeId ? $storeId : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS
            ));

            if(!count($this->getCollection()->getItems()) > 0) {
                continue;
            }

            /** @var $orderHelper Mage_XmlConnect_Helper_Adminhtml_Dashboard_Order */
            $orderHelper = Mage::helper('xmlconnect/adminhtml_dashboard_order');

            foreach ($this->getCollection()->getItems() as $item) {
                $itemListXml = $valuesXml->addCustomChild('item');
                $itemListXml->addCustomChild('name', $item->getName(), array(
                    'label' => Mage::helper('reports')->__('Product Name')
                ));
                $itemListXml->addCustomChild(
                    'price',
                    $orderHelper->preparePrice($item->getPrice(), $storeId),
                    array('label' => Mage::helper('reports')->__('Price'))
                );
                $itemListXml->addCustomChild(
                    'views',
                    $item->getViews(),
                    array('label' => Mage::helper('reports')->__('Number of Views'))
                );
            }
        }
        return $this;
    }
}
