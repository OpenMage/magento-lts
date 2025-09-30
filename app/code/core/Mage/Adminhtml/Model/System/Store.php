<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml System Store Model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Store extends Varien_Object
{
    /**
     * Website collection
     * websiteId => Mage_Core_Model_Website
     *
     * @var array
     */
    protected $_websiteCollection = [];

    /**
     * Group collection
     * groupId => Mage_Core_Model_Store_Group
     *
     * @var array
     */
    protected $_groupCollection = [];

    /**
     * Store collection
     * storeId => Mage_Core_Model_Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * @var bool
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private $_isAdminScopeAllowed = true;

    /**
     * Init model
     * Load Website, Group and Store collections
     */
    public function __construct()
    {
        $this->reload();
    }

    /**
     * Load/Reload Website collection
     *
     * @return $this
     */
    protected function _loadWebsiteCollection()
    {
        $this->_websiteCollection = Mage::app()->getWebsites();
        return $this;
    }

    /**
     * Load/Reload Group collection
     *
     * @return $this
     */
    protected function _loadGroupCollection()
    {
        $this->_groupCollection = [];
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $this->_groupCollection[$group->getId()] = $group;
            }
        }
        return $this;
    }

    /**
     * Load/Reload Store collection
     *
     * @return $this
     */
    protected function _loadStoreCollection()
    {
        $this->_storeCollection = Mage::app()->getStores();
        return $this;
    }

    /**
     * Retrieve store values for form
     *
     * @param bool $empty
     * @param bool $all
     * @return array
     */
    public function getStoreValuesForForm($empty = false, $all = false)
    {
        $options = [];
        if ($empty) {
            $options[] = [
                'label' => '',
                'value' => '',
            ];
        }
        if ($all && $this->_isAdminScopeAllowed) {
            $options[] = [
                'label' => Mage::helper('adminhtml')->__('All Store Views'),
                'value' => 0,
            ];
        }

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        foreach ($this->_websiteCollection as $website) {
            $websiteShow = false;
            foreach ($this->_groupCollection as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $values    = [];
                $groupShow = false;
                foreach ($this->_storeCollection as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $options[] = [
                            'label' => Mage::helper('core')->escapeHtml($website->getName()),
                            'value' => [],
                        ];
                        $websiteShow = true;
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $values    = [];
                    }
                    $values[] = [
                        'label' => str_repeat($nonEscapableNbspChar, 4) .
                            Mage::helper('core')->escapeHtml($store->getName()),
                        'value' => $store->getId(),
                    ];
                }
                if ($groupShow) {
                    $options[] = [
                        'label' => str_repeat($nonEscapableNbspChar, 4) .
                            Mage::helper('core')->escapeHtml($group->getName()),
                        'value' => $values,
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * Retrieve stores structure
     *
     * @param bool $isAll
     * @param array $storeIds
     * @param array $groupIds
     * @param array $websiteIds
     * @return array
     */
    public function getStoresStructure($isAll = false, $storeIds = [], $groupIds = [], $websiteIds = [])
    {
        $out = [];
        $websites = $this->getWebsiteCollection();

        if ($isAll) {
            $out[] = [
                'value' => 0,
                'label' => Mage::helper('adminhtml')->__('All Store Views'),
            ];
        }

        foreach ($websites as $website) {
            $websiteId = $website->getId();
            if ($websiteIds && !in_array($websiteId, $websiteIds)) {
                continue;
            }
            $out[$websiteId] = [
                'value' => $websiteId,
                'label' => $website->getName(),
            ];

            foreach ($website->getGroups() as $group) {
                $groupId = $group->getId();
                if ($groupIds && !in_array($groupId, $groupIds)) {
                    continue;
                }
                $out[$websiteId]['children'][$groupId] = [
                    'value' => $groupId,
                    'label' => $group->getName(),
                ];

                foreach ($group->getStores() as $store) {
                    $storeId = $store->getId();
                    if ($storeIds && !in_array($storeId, $storeIds)) {
                        continue;
                    }
                    $out[$websiteId]['children'][$groupId]['children'][$storeId] = [
                        'value' => $storeId,
                        'label' => $store->getName(),
                    ];
                }
                if (empty($out[$websiteId]['children'][$groupId]['children'])) {
                    unset($out[$websiteId]['children'][$groupId]);
                }
            }
            if (empty($out[$websiteId]['children'])) {
                unset($out[$websiteId]);
            }
        }
        return $out;
    }

    /**
     * Website label/value array getter, compatible with form dropdown options
     *
     * @param bool $empty
     * @param bool $all
     * @return array
     */
    public function getWebsiteValuesForForm($empty = false, $all = false)
    {
        $options = [];
        if ($empty) {
            $options[] = [
                'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
                'value' => '',
            ];
        }
        if ($all && $this->_isAdminScopeAllowed) {
            $options[] = [
                'label' => Mage::helper('adminhtml')->__('Admin'),
                'value' => 0,
            ];
        }

        foreach ($this->_websiteCollection as $website) {
            $options[] = [
                'label' => $website->getName(),
                'value' => $website->getId(),
            ];
        }
        return $options;
    }

    /**
     * Get websites as id => name associative array
     *
     * @param bool $withDefault
     * @param string $attribute
     * @return array
     */
    public function getWebsiteOptionHash($withDefault = false, $attribute = 'name')
    {
        $options = [];
        foreach (Mage::app()->getWebsites((bool) $withDefault && $this->_isAdminScopeAllowed) as $website) {
            $options[$website->getId()] = $website->getDataUsingMethod($attribute);
        }
        return $options;
    }

    /**
     * Get store views as id => name associative array
     *
     * @param bool $withDefault
     * @param string $attribute
     * @return array
     */
    public function getStoreOptionHash($withDefault = false, $attribute = 'name')
    {
        $options = [];
        foreach (Mage::app()->getStores((bool) $withDefault && $this->_isAdminScopeAllowed) as $store) {
            $options[$store->getId()] = $store->getDataUsingMethod($attribute);
        }
        return $options;
    }

    /**
     * Get store groups as id => name associative array
     *
     * @param string $attribute
     * @return array
     */
    public function getStoreGroupOptionHash($attribute = 'name')
    {
        $options = [];
        foreach ($this->_groupCollection as $group) {
            $options[$group->getId()] = $group->getDataUsingMethod($attribute);
        }
        return $options;
    }

    /**
     * Retrieve Website name by Id
     *
     * @param int $websiteId
     * @return string|null
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
     * @param int $groupId
     * @return string|null
     */
    public function getGroupName($groupId)
    {
        foreach ($this->_groupCollection as $group) {
            if ($group->getId() == $groupId) {
                return $group->getName();
            }
        }
        return null;
    }

    /**
     * Retrieve Store name by Id
     *
     * @param int $storeId
     * @return string|null
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
        return $this->_storeCollection[$storeId] ?? null;
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
            $names = [];
            foreach ($storeId as $id) {
                $names[] = $this->getStoreNameWithWebsite($id);
            }
            $name = implode(', ', $names);
        } elseif (isset($this->_storeCollection[$storeId])) {
            $data = $this->_storeCollection[$storeId];
            $name .= $this->getWebsiteName($data->getWebsiteId());
            $name .= ($name ? '/' : '') . $this->getGroupName($data->getGroupId());
            $name .= ($name ? '/' : '') . $data->getName();
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
     * @return $this
     */
    public function reload($type = null)
    {
        if (is_null($type)) {
            $this->_loadWebsiteCollection();
            $this->_loadGroupCollection();
            $this->_loadStoreCollection();
        } else {
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
            $names = [];
            foreach ($storeId as $id) {
                $names[] = $this->getStoreNamePath($id);
            }
            $name = implode(', ', $names);
        } elseif (isset($this->_storeCollection[$storeId])) {
            $data = $this->_storeCollection[$storeId];
            $name .= $this->getWebsiteName($data->getWebsiteId());
            $name .= ($name ? '/' : '') . $this->getGroupName($data->getGroupId());
        }
        return $name;
    }

    /**
     * Specify whether to show admin-scope options
     *
     * @param bool $value
     * @return $this
     */
    public function setIsAdminScopeAllowed($value)
    {
        $this->_isAdminScopeAllowed = (bool) $value;
        return $this;
    }
}
