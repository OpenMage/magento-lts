<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * @var array
     */
    protected $_storeToWebsite   = null;

    /**
     * @param string $table
     * @return $this
     */
    public function convertStoreToWebsite($table)
    {
        $assignment = $this->_getStoreToWebsiteAssignments();
        foreach ($assignment as $website => $stores) {
            $this->_setWebsiteInfo($table, $website, $stores);
        }
        return $this;
    }

    /**
     * @return Mage_Core_Model_Website[]
     */
    protected function _getStoreToWebsiteAssignments()
    {
        if (is_null($this->_storeToWebsite)) {
            $this->_storeToWebsite = [];
            $websiteCollection = Mage::getModel('core/website')->getCollection();
            foreach ($websiteCollection as $website) {
                $this->_storeToWebsite[$website->getId()] = $website->getStoreIds();
            }
        }

        return $this->_storeToWebsite;
    }

    /**
     * @param string $table
     * @param int $websiteId
     * @param array $storeIds
     * @return $this
     */
    protected function _setWebsiteInfo($table, $websiteId, $storeIds)
    {
        $this->getConnection()->update(
            $table,
            ['website_id'=>$websiteId],
            $this->getConnection()->quoteInto('store_id IN (?)', $storeIds)
        );

        return $this;
    }
}
