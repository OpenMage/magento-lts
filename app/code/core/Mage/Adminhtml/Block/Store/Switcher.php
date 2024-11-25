<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store switcher block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Store_Switcher extends Mage_Adminhtml_Block_Template
{
    /**
     * @var array
     */
    protected $_storeIds;

    /**
     * Name of store variable
     *
     * @var string
     */
    protected $_storeVarName = 'store';

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('store/switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName($this->__('All Store Views'));
    }

    /**
     * @return Mage_Core_Model_Resource_Website_Collection
     * @throws Mage_Core_Exception
     * @deprecated
     */
    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('core/website')->getResourceCollection();

        $websiteIds = $this->getWebsiteIds();
        if (!is_null($websiteIds)) {
            $collection->addIdFilter($this->getWebsiteIds());
        }

        return $collection->load();
    }

    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $websites = Mage::app()->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            foreach (array_keys($websites) as $websiteId) {
                if (!in_array($websiteId, $websiteIds)) {
                    unset($websites[$websiteId]);
                }
            }
        }
        return $websites;
    }

    /**
     * @param Mage_Core_Model_Website|int|string $website
     * @return Mage_Core_Model_Resource_Store_Group_Collection
     * @deprecated
     */
    public function getGroupCollection($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::getModel('core/website')->load($website);
        }
        return $website->getGroupCollection();
    }

    /**
     * Get store groups for specified website
     *
     * @param Mage_Core_Model_Website|int|string|null $website
     * @return array
     */
    public function getStoreGroups($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::app()->getWebsite($website);
        }
        return $website->getGroups();
    }

    /**
     * @param Mage_Core_Model_Store_Group|int|string $group
     * @return Mage_Core_Model_Resource_Store_Collection
     * @deprecated
     */
    public function getStoreCollection($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::getModel('core/store_group')->load($group);
        }
        $stores = $group->getStoreCollection();
        $storeIds = $this->getStoreIds();
        if (!empty($storeIds)) {
            $stores->addIdFilter($storeIds);
        }
        return $stores;
    }

    /**
     * Get store views for specified store group
     *
     * @param Mage_Core_Model_Store_Group|int|string|null $group
     * @return array
     */
    public function getStores($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::app()->getGroup($group);
        }
        $stores = $group->getStores();
        if ($storeIds = $this->getStoreIds()) {
            foreach (array_keys($stores) as $storeId) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }
        return $stores;
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', ['_current' => true, $this->_storeVarName => null]);
    }

    /**
     * @param string $varName
     * @return $this
     */
    public function setStoreVarName($varName)
    {
        $this->_storeVarName = $varName;
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getStoreId()
    {
        return $this->getRequest()->getParam($this->_storeVarName);
    }

    /**
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if ($hasDefaultOption !== null) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }
}
