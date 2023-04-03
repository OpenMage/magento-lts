<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Index_Model_Observer
{
    /**
     * Indexer model
     *
     * @var Mage_Index_Model_Indexer
     */
    protected $_indexer;

    public function __construct()
    {
        $this->_indexer = Mage::getSingleton('index/indexer');
    }

    /**
     * Store after commit observer. Process store related indexes
     *
     * @param Varien_Event_Observer $observer
     */
    public function processStoreSave(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_indexer->processEntityAction(
            $store,
            Mage_Core_Model_Store::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * Store group after commit observer. Process store group related indexes
     *
     * @param Varien_Event_Observer $observer
     */
    public function processStoreGroupSave(Varien_Event_Observer $observer)
    {
        $storeGroup = $observer->getEvent()->getStoreGroup();
        $this->_indexer->processEntityAction(
            $storeGroup,
            Mage_Core_Model_Store_Group::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * Website save after commit observer. Process website related indexes
     *
     * @param Varien_Event_Observer $observer
     */
    public function processWebsiteSave(Varien_Event_Observer $observer)
    {
        $website = $observer->getEvent()->getWebsite();
        $this->_indexer->processEntityAction(
            $website,
            Mage_Core_Model_Website::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * Store after commit observer. Process store related indexes
     *
     * @param Varien_Event_Observer $observer
     */
    public function processStoreDelete(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_indexer->processEntityAction(
            $store,
            Mage_Core_Model_Store::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
    }

    /**
     * Store group after commit observer. Process store group related indexes
     *
     * @param Varien_Event_Observer $observer
     */
    public function processStoreGroupDelete(Varien_Event_Observer $observer)
    {
        $storeGroup = $observer->getEvent()->getStoreGroup();
        $this->_indexer->processEntityAction(
            $storeGroup,
            Mage_Core_Model_Store_Group::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
    }

    /**
     * Website save after commit observer. Process website related indexes
     *
     * @param Varien_Event_Observer $observer
     */
    public function processWebsiteDelete(Varien_Event_Observer $observer)
    {
        $website = $observer->getEvent()->getWebsite();
        $this->_indexer->processEntityAction(
            $website,
            Mage_Core_Model_Website::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
    }

    /**
     * Config data after commit observer.
     *
     * @param Varien_Event_Observer $observer
     */
    public function processConfigDataSave(Varien_Event_Observer $observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        $this->_indexer->processEntityAction(
            $configData,
            Mage_Core_Model_Config_Data::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }
}
