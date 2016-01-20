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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog extends Mage_Catalog_Block_Product_List_Toolbar
{
    /**
     * Limit for product sorting fields to return
     */
    const PRODUCT_SORT_FIELDS_NUMBER = 3;

    /**
     * Prefix that used in specifying filters on request
     *
     * @deprecated renamed
     */
    const REQUEST_FILTER_PARAM_REFIX = self::REQUEST_FILTER_PARAM_PREFIX;

    /**
     * Prefix that used in specifying filters on request
     */
    const REQUEST_FILTER_PARAM_PREFIX = 'filter_';

    /**
     * Prefix that used in specifying sort order params on request
     *
     * @deprecated renamed
     */
    const REQUEST_SORT_ORDER_PARAM_REFIX = self::REQUEST_SORT_ORDER_PARAM_PREFIX;

    /**
     * Prefix that used in specifying sort order params on request
     */
    const REQUEST_SORT_ORDER_PARAM_PREFIX = 'order_';

    /**
     * Retrieve product sort fields as xml object
     *
     * @deprecated method renamed
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductSortFeildsXmlObject()
    {
        $this->getProductSortFieldsXmlObject();
    }

    /**
     * Retrieve product sort fields as xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductSortFieldsXmlObject()
    {
        /** @var $ordersXmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $ordersXmlObject    = Mage::getModel('xmlconnect/simplexml_element', '<orders></orders>');
        /* @var $category Mage_Catalog_Model_Category */
        $category           = Mage::getModel('catalog/category');
        $sortOptions        = $category->getAvailableSortByOptions();
        $sortOptions        = array_slice($sortOptions, 0, self::PRODUCT_SORT_FIELDS_NUMBER);
        $defaultSort        = $category->getDefaultSortBy();
        foreach ($sortOptions as $code => $name) {
            $item = $ordersXmlObject->addChild('item');
            if ($code == $defaultSort) {
                $item->addAttribute('isDefault', 1);
            }
            $item->addChild('code', $code);
            $item->addChild('name', $ordersXmlObject->escapeXml($name));
        }

        return $ordersXmlObject;
    }

    /**
     * Retrieve catalog search product sort fields as xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getSearchProductSortFieldsXmlObject()
    {
        /** @var $ordersXmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $ordersXmlObject    = Mage::getModel('xmlconnect/simplexml_element', '<orders></orders>');
        /* @var $category Mage_Catalog_Model_Category */
        $category           = Mage::getModel('catalog/category');
        $sortOptions        = $category->getAvailableSortByOptions();
        $sortOptions        = array_slice($sortOptions, 0, self::PRODUCT_SORT_FIELDS_NUMBER);
        unset($sortOptions['position']);
        $sortOptions        = array_merge(array('relevance' => $this->__('Relevance')), $sortOptions);
        $this->setAvailableOrders($sortOptions)->setDefaultDirection('desc')->setSortBy('relevance');

        foreach($this->getAvailableOrders() as $key => $order) {
            $item = $ordersXmlObject->addChild('item');
            if ($this->isOrderCurrent($key)) {
                $item->addAttribute('isDefault', 1);
            }
            $item->addChild('code', $key);
            $item->addChild('name', $ordersXmlObject->escapeXml($order));
        }
        return $ordersXmlObject;
    }
}
