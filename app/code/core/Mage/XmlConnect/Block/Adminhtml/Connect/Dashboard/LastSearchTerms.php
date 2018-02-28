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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin application last search terms xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastSearchTerms
    extends Mage_Adminhtml_Block_Dashboard_Searches_Last
{
    /**
     * Search terms count to display
     */
    const TERMS_COUNT_LIMIT = 5;

    /**
     * Get rid of unnecessary collection initialization
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastSearchTerms
     */
    protected function _prepareCollection()
    {
        return $this;
    }

    /**
     * Init last search terms collection
     *
     * @param int|null $storeId
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastSearchTerms
     */
    protected function _initCollection($storeId)
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_CatalogSearch')) {
            return $this;
        }
        /** @var $_collection Mage_CatalogSearch_Model_Resource_Query_Collection */
        $this->_collection = Mage::getModel('catalogsearch/query')->getResourceCollection();
        $this->_collection->setRecentQueryFilter()->setPageSize(self::TERMS_COUNT_LIMIT);

        if ($storeId) {
            $this->_collection->addFieldToFilter('store_id', $storeId);
        }
        $this->setCollection($this->_collection);
        return $this;
    }

    /**
     * Clear collection
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastSearchTerms
     */
    protected function _clearCollection()
    {
        $this->_collection = null;
        return $this;
    }

    /**
     * Add last search terms info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastSearchTerms
     */
    public function addLastSearchTermsToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $storeId) {
            $this->_clearCollection()->_initCollection($storeId);
            $valuesXml = $xmlObj->addCustomChild('values', null, array(
                'store_id' => $storeId ? $storeId : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS
            ));

            if(!count($this->getCollection()->getItems()) > 0) {
                continue;
            }

            foreach ($this->getCollection()->getItems() as $item) {
                $itemListXml = $valuesXml->addCustomChild('item');
                $itemListXml->addCustomChild('query_text', $item->getQueryText(), array(
                    'label' => $this->__('Search Term')
                ));
                $itemListXml->addCustomChild('num_results', $item->getNumResults(), array(
                    'label' => $this->__('Results')
                ));
                $itemListXml->addCustomChild('popularity', $item->getPopularity(), array(
                    'label' => $this->__('Number of Uses')
                ));
            }
        }
        return $this;
    }
}
