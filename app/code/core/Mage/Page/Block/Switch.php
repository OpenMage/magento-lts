<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * Store and language switcher block
 *
 * @package    Mage_Page
 */
class Mage_Page_Block_Switch extends Mage_Core_Block_Template
{
    protected $_storeInUrl;

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentWebsiteId()
    {
        return Mage::app()->getStore()->getWebsiteId();
    }

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentGroupId()
    {
        return Mage::app()->getStore()->getGroupId();
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
     * @return Mage_Core_Model_Store_Group[]
     * @throws Mage_Core_Exception
     */
    public function getRawGroups()
    {
        if (!$this->hasData('raw_groups')) {
            $websiteGroups = Mage::app()->getWebsite()->getGroups();

            $groups = [];
            foreach ($websiteGroups as $group) {
                $groups[$group->getId()] = $group;
            }
            $this->setData('raw_groups', $groups);
        }
        return $this->getData('raw_groups');
    }

    /**
     * @return Mage_Core_Model_Store[]
     * @throws Mage_Core_Exception
     */
    public function getRawStores()
    {
        if (!$this->hasData('raw_stores')) {
            $websiteStores = Mage::app()->getWebsite()->getStores();
            $stores = [];
            foreach ($websiteStores as $store) {
                if (!$store->getIsActive()) {
                    continue;
                }
                $store->setLocaleCode(Mage::getStoreConfig('general/locale/code', $store->getId()));

                $params = [
                    '_query' => [],
                ];
                if (!$this->isStoreInUrl()) {
                    $params['_query']['___store'] = $store->getCode();
                }
                $baseUrl = $store->getUrl('', $params);

                $store->setHomeUrl($baseUrl);
                $stores[$store->getGroupId()][$store->getId()] = $store;
            }
            $this->setData('raw_stores', $stores);
        }
        return $this->getData('raw_stores');
    }

    /**
     * Retrieve list of store groups with default urls set
     *
     * @return Mage_Core_Model_Store_Group[]
     */
    public function getGroups()
    {
        if (!$this->hasData('groups')) {
            $rawGroups = $this->getRawGroups();
            $rawStores = $this->getRawStores();

            $groups = [];
            $localeCode = Mage::getStoreConfig('general/locale/code');
            foreach ($rawGroups as $group) {
                if (!isset($rawStores[$group->getId()])) {
                    continue;
                }
                if ($group->getId() == $this->getCurrentGroupId()) {
                    $groups[] = $group;
                    continue;
                }

                $store = $group->getDefaultStoreByLocale($localeCode);

                if ($store) {
                    $group->setHomeUrl($store->getHomeUrl());
                    $groups[] = $group;
                }
            }
            $this->setData('groups', $groups);
        }
        return $this->getData('groups');
    }

    /**
     * @return Mage_Core_Model_Store[]
     */
    public function getStores()
    {
        if (!$this->getData('stores')) {
            $rawStores = $this->getRawStores();

            $groupId = $this->getCurrentGroupId();
            if (!isset($rawStores[$groupId])) {
                $stores = [];
            } else {
                $stores = $rawStores[$groupId];
            }
            $this->setData('stores', $stores);
        }
        return $this->getData('stores');
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentStoreCode()
    {
        return Mage::app()->getStore()->getCode();
    }

    /**
     * @return bool
     */
    public function isStoreInUrl()
    {
        if (is_null($this->_storeInUrl)) {
            $this->_storeInUrl = Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL);
        }
        return $this->_storeInUrl;
    }
}
