<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Store switcher block
 *
 * @package    Mage_Core
 *
 * @method array getLanguages()
 * @method $this setLanguages(array $value)
 * @method array getStores()
 * @method $this setStores(array $value)
 */
class Mage_Core_Block_Store_Switcher extends Mage_Core_Block_Template
{
    protected $_groups = [];
    protected $_stores = [];
    protected $_loaded = false;

    public function __construct()
    {
        $this->_loadData();
        $this->setStores([]);
        $this->setLanguages([]);
        parent::__construct();
    }

    /**
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _loadData()
    {
        if ($this->_loaded) {
            return $this;
        }

        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $storeCollection = Mage::getModel('core/store')
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        $groupCollection = Mage::getModel('core/store_group')
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        /** @var Mage_Core_Model_Store_Group $group */
        foreach ($groupCollection as $group) {
            $this->_groups[$group->getId()] = $group;
        }
        /** @var Mage_Core_Model_Store $store */
        foreach ($storeCollection as $store) {
            if (!$store->getIsActive()) {
                continue;
            }
            $store->setLocaleCode(Mage::getStoreConfig('general/locale/code', $store->getId()));
            $this->_stores[$store->getGroupId()][$store->getId()] = $store;
        }

        $this->_loaded = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreCount()
    {
        $stores = [];
        $localeCode = Mage::getStoreConfig('general/locale/code');
        foreach ($this->_groups as $group) {
            if (!isset($this->_stores[$group->getId()])) {
                continue;
            }
            $useStore = false;
            /** @var Mage_Core_Model_Store $store */
            foreach ($this->_stores[$group->getId()] as $store) {
                if ($store->getLocaleCode() == $localeCode) {
                    $useStore = true;
                    $stores[] = $store;
                }
            }
            if (!$useStore && isset($this->_stores[$group->getId()][$group->getDefaultStoreId()])) {
                $stores[] = $this->_stores[$group->getId()][$group->getDefaultStoreId()];
            }
        }

        $this->setStores($stores);
        return count($this->getStores());
    }

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getLanguageCount()
    {
        $groupId = Mage::app()->getStore()->getGroupId();
        if (!isset($this->_stores[$groupId])) {
            $this->setLanguages([]);
            return 0;
        }
        $this->setLanguages($this->_stores[$groupId]);
        return count($this->getLanguages());
    }

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentStoreCode()
    {
        return Mage::app()->getStore()->getCode();
    }
}
