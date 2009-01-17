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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml System Store Model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Store extends Varien_Object
{

    /**
     * Website collection
     * websiteId => Mage_Core_Model_Website
     *
     * @var array
     */
    protected $_websiteCollection = array();

    /**
     * Group collection
     * groupId => Mage_Core_Model_Store_Group
     *
     * @var array
     */
    protected $_groupCollection = array();

    /**
     * Store collection
     * storeId => Mage_Core_Model_Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * Init model
     * Load Website, Group and Store collections
     *
     * @return Mage_Adminhtml_Model_System_Store
     */
    public function __construct()
    {
        return $this->reload();
    }

    /**
     * Load/Reload Website collection
     *
     * @return array
     */
    protected function _loadWebsiteCollection()
    {
        $this->_websiteCollection = array();
        foreach (Mage::getModel('core/website')->getCollection() as $website) {
            $this->_websiteCollection[$website->getId()] = $website;
        }
        return $this;
    }

    /**
     * Load/Reload Group collection
     *
     * @return array
     */
    protected function _loadGroupCollection()
    {
        $this->_groupCollection = array();
        foreach (Mage::getModel('core/store_group')->getCollection() as $group) {
            $this->_groupCollection[$group->getId()] = $group;
        }
        return $this;
    }

    /**
     * Load/Reload Store collection
     *
     * @return array
     */
    protected function _loadStoreCollection()
    {
        $this->_storeCollection = array();
        foreach (Mage::getModel('core/store')->getCollection() as $store) {
            $this->_storeCollection[$store->getId()] = $store;
        }
        return $this;
    }

    public function getStoreValuesForForm($empty = false, $all = false)
    {
        $options = array();
        if ($empty) {
            $options[] = array(
                'label' => '',
                'value' => ''
            );
        }
        if ($all) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('All Store Views'),
                'value' => 0
            );
        }

        foreach ($this->_websiteCollection as $website) {
            $websiteShow = false;
            foreach ($this->_groupCollection as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($this->_storeCollection as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $options[] = array(
                            'label' => $website->getName(),
                            'value' => array()
                        );
                        $websiteShow = true;
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $values    = array();
                    }
                    $values[] = array(
                        'label' => '&nbsp;&nbsp;&nbsp;&nbsp;' . $store->getName(),
                        'value' => $store->getId()
                    );
                }
                if ($groupShow) {
                    $options[] = array(
                        'label' => '&nbsp;&nbsp;&nbsp;&nbsp;' . $group->getName(),
                        'value' => $values
                    );
                }
            }
        }
        return $options;
    }

    public function getWebsiteValuesForForm($empty = false, $all = false)
    {
        $options = array();
        if ($empty) {
            $options[] = array(
                'label' => '',
                'value' => ''
            );
        }
        if ($all) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('Admin'),
                'value' => 0
            );
        }

        foreach ($this->_websiteCollection as $website) {
            $options[] = array(
                'label' => $website->getName(),
                'value' => $website->getId(),
            );
        }
        return $options;
    }

    /**
     * Retrieve Website name by Id
     *
     * @param int websiteId
     * @return string
     */
    public function getWebsiteName($websiteId)
    {
        foreach ($this->_websiteCollection as $website) {
            if ($website->getId() == $websiteId) {
                return $website->getName();
            }
        }
        return null;
    }

    /**
     * Retrieve Group name by Id
     *
     * @param int groupId
     * @return string
     */
    public function getGroupName($groupId)
    {
        foreach ($this->_groupCollection as $group) {
            if ($group->getId() == $groupId) {
                return $group->getName();
            }
        }
        return $null;
    }

    /**
     * Retrieve Store name by Id
     *
     * @param int $storeId
     * @return string
     */
    public function getStoreName($storeId)
    {
        if (isset($this->_storeCollection[$storeId])) {
            return $this->_storeCollection[$storeId]->getName();
        }
        return null;
    }

    /**
     * Retrieve store name with website and website store
     *
     * @param  int $storeId
     * @return Mage_Core_Model_Store
     **/
    public function getStoreData($storeId)
    {
        if (isset($this->_storeCollection[$storeId])) {
            return $this->_storeCollection[$storeId];
        }
        return null;
    }

    /**
     * Retrieve store name with website and website store
     *
     * @param  int $storeId
     * @return string
     **/
    public function getStoreNameWithWebsite($storeId)
    {
        $name = '';
        if (is_array($storeId)) {
            $names = array();
            foreach ($storeId as $id) {
            	$names[]= $this->getStoreNameWithWebsite($id);
            }
            $name = implode(', ', $names);
        }
        else {
            if (isset($this->_storeCollection[$storeId])) {
                $data = $this->_storeCollection[$storeId];
                $name .= $this->getWebsiteName($data->getWebsiteId());
                $name .= ($name ? '/' : '').$this->getGroupName($data->getGroupId());
                $name .= ($name ? '/' : '').$data->getName();
            }
        }
        return $name;
    }

    /**
     * Retrieve Website collection as array
     *
     * @return array
     */
    public function getWebsiteCollection()
    {
        return $this->_websiteCollection;
    }

    /**
     * Retrieve Group collection as array
     *
     * @return array
     */
    public function getGroupCollection()
    {
        return $this->_groupCollection;
    }

    /**
     * Retrieve Store collection as array
     *
     * @return array
     */
    public function getStoreCollection()
    {
        return $this->_storeCollection;
    }

    /**
     * Load/Reload collection(s) by type
     * Allowed types: website, group, store or null for all
     *
     * @param string $type
     * @return Mage_Adminhtml_Model_System_Store
     */
    public function reload($type = null)
    {
        if (is_null($type)) {
            $this->_loadWebsiteCollection();
            $this->_loadGroupCollection();
            $this->_loadStoreCollection();
        }
        else {
            switch ($type) {
                case 'website':
                    $this->_loadWebsiteCollection();
                    break;
                case 'group':
                    $this->_loadGroupCollection();
                    break;
                case 'store':
                    $this->_loadStoreCollection();
                    break;
                default:
                    break;
            }
        }
        return $this;
    }

    /**
     * Retrieve store path with website and website store
     *
     * @param  int $storeId
     * @return string
     **/
    public function getStoreNamePath($storeId)
    {
        $name = '';
        if (is_array($storeId)) {
            $names = array();
            foreach ($storeId as $id) {
            	$names[]= $this->getStoreNamePath($id);
            }
            $name = implode(', ', $names);
        }
        else {
            if (isset($this->_storeCollection[$storeId])) {
                $data = $this->_storeCollection[$storeId];
                $name .= $this->getWebsiteName($data->getWebsiteId());
                $name .= ($name ? '/' : '').$this->getGroupName($data->getGroupId());
            }
        }
        return $name;
    }
}
