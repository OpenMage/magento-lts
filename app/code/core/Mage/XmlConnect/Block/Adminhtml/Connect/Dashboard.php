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
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin application dashboard renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard extends Mage_Core_Block_Abstract
{
    /**
     * Simple xml object
     *
     * @var Mage_XmlConnect_Model_Simplexml_Element
     */
    protected $_xmlObject;

    /**
     * Render dashboard xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->setXmlObject(Mage::getModel('xmlconnect/simplexml_element', '<dashboard></dashboard>'))
            ->_addStoreSwitcher()->_addSalesInfo()->_addGraphInfo()->_addDashboardFormData()->getXmlObject()
            ->asNiceXml();
    }

    /**
     * Add store switcher xml
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addStoreSwitcher()
    {
        $this->getChild('store_switcher')->addSwitcherToXmlObj($this->getXmlObject());
        return $this;
    }

    /**
     * Add sales info
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addSalesInfo()
    {
        $this->getChild('sales_info')->addSalesInfoToXmlObj($this->getXmlObject());
        return $this;
    }

    /**
     * Add graph info to xml object
     *
     * Add orders and amounts info to show diagram by selected range in application
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addGraphInfo()
    {
        $this->getChild('graph_info')->addGraphInfoToXmlObj($this->getXmlObject());
        return $this;
    }

    /**
     * Add dashboard form data
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addDashboardFormData()
    {
        /** @var Mage_XmlConnect_Model_Simplexml_Form $fromXmlObj */
        $fromXmlObj = Mage::getModel('xmlconnect/simplexml_form', array(
            'xml_id' => 'dashboard_form', 'action' => '', 'use_container' => true
        ));

        $recentActivityFieldset = $fromXmlObj->addFieldset('recent_activity', array(
            'title' => $this->__('Recent Activity')
        ));

        $this->_addLastOrders($recentActivityFieldset)->_addLastSearchTerms($recentActivityFieldset)
            ->_addNewCustomers($recentActivityFieldset);

        $overallActivityFieldset = $fromXmlObj->addFieldset('overall_activity', array(
            'title' => $this->__('Overall Activity')
        ));

        $this->_addTopSearchTerms($overallActivityFieldset)->_addMostViewedProducts($overallActivityFieldset)
            ->_addBestSellers($overallActivityFieldset)->_addCustomers($overallActivityFieldset);

        $this->getXmlObject()->appendChild($fromXmlObj->toXmlObject());
        return $this;
    }

    /**
     * Add last orders info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $recentActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addLastOrders(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $recentActivityFieldset)
    {
        $lastOrdersField = $recentActivityFieldset->addField('last_orders', 'custom', array(
            'label' => $this->__('Last 5 Orders')
        ));
        $this->getChild('last_orders')->addLastOrdersToXmlObj($lastOrdersField->getXmlObject());
        return $this;
    }

    /**
     * Add last search terms data
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $recentActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addLastSearchTerms(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $recentActivityFieldset
    ) {
        $lastSearchTermsField = $recentActivityFieldset->addField('last_search', 'custom', array(
            'label' => $this->__('Last 5 Search Terms')
        ));
        $this->getChild('last_search_terms')->addLastSearchTermsToXmlObj($lastSearchTermsField->getXmlObject());
        return $this;
    }

    /**
     * Add new customers info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $recentActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addNewCustomers(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $recentActivityFieldset)
    {
        $newCustomersField = $recentActivityFieldset->addField('new_customers', 'custom', array(
            'label' => $this->__('New Customers')
        ));
        $this->getChild('new_customers')->addNewCustomersToXmlObj($newCustomersField->getXmlObject());
        return $this;
    }

    /**
     * Add top search queries info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addTopSearchTerms(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset
    ) {
        $topSearchField = $overallActivityFieldset->addField('top_search', 'custom', array(
            'label' => $this->__('Top Search Terms')
        ));
        $this->getChild('top_search_terms')->addTopSearchTermsToXmlObj($topSearchField->getXmlObject());
        return $this;
    }

    /**
     * Add most viewed products to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addMostViewedProducts(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset
    ) {
        $mostViewedField = $overallActivityFieldset->addField('most_viewed', 'custom', array(
            'label' => $this->__('Most Viewed Products')
        ));
        $this->getChild('most_viewed')->addMostViewedProductsToXmlObj($mostViewedField->getXmlObject());
        return $this;
    }

    /**
     * Add best sellers info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addBestSellers(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset)
    {
        $bestSellersField = $overallActivityFieldset->addField('best_sellers', 'custom', array(
            'label' => $this->__('Best Sellers')
        ));
        $this->getChild('best_sellers')->addBestSellersToXmlObj($bestSellersField->getXmlObject());
        return $this;
    }

    /**
     * Add customer info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    protected function _addCustomers(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $overallActivityFieldset)
    {
        $customersField = $overallActivityFieldset->addField('customers', 'custom', array(
            'label' => $this->__('Customers')
        ));
        $this->getChild('customers')->addCustomersToXmlObj($customersField->getXmlObject());
        return $this;
    }

    /**
     * Get xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getXmlObject()
    {
        return $this->_xmlObject;
    }

    /**
     * Set xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObject
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard
     */
    public function setXmlObject($xmlObject)
    {
        $this->_xmlObject = $xmlObject;
        return $this;
    }
}
