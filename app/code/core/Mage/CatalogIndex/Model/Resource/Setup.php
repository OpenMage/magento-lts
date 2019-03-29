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
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_storeToWebsite   = null;

    /**
     * Enter description here ...
     *
     * @param unknown_type $table
     * @return Mage_CatalogIndex_Model_Resource_Setup
     */
    public function convertStoreToWebsite($table)
    {
        $assignment = $this->_getStoreToWebsiteAssignments();
        foreach ($assignment as $website=>$stores) {
            $this->_setWebsiteInfo($table, $website, $stores);
        }
        return $this;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    protected function _getStoreToWebsiteAssignments()
    {
        if (is_null($this->_storeToWebsite)) {
            $this->_storeToWebsite = array();
            $websiteCollection = Mage::getModel('core/website')->getCollection();
            foreach ($websiteCollection as $website) {
                $this->_storeToWebsite[$website->getId()] = $website->getStoreIds();
            }
        }

        return $this->_storeToWebsite;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $table
     * @param unknown_type $websiteId
     * @param unknown_type $storeIds
     * @return Mage_CatalogIndex_Model_Resource_Setup
     */
    protected function _setWebsiteInfo($table, $websiteId, $storeIds)
    {
        $this->getConnection()->update(
            $table,
            array('website_id'=>$websiteId),
            $this->getConnection()->quoteInto('store_id IN (?)', $storeIds)
        );

        return $this;
    }
}
