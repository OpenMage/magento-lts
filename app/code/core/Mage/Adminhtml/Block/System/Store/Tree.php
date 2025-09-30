<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml store tree
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Tree extends Mage_Adminhtml_Block_Widget
{
    /**
     * Cell Template
     *
     * @var Mage_Adminhtml_Block_Template
     */
    protected $_cellTemplate;

    /**
     * Internal constructor, that is called from real constructor
     */
    public function _construct()
    {
        $this->setTemplate('system/store/tree.phtml');
        parent::_construct();
    }

    /**
     * Prepare block layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->_cellTemplate = $this->getLayout()
            ->createBlock('adminhtml/template')
            ->setTemplate('system/store/cell.phtml');
        return parent::_prepareLayout();
    }

    /**
     * Get table data
     *
     * @return array
     */
    public function getTableData()
    {
        $data = [];
        foreach (Mage::getModel('core/website')->getCollection() as $website) {
            /** @var Mage_Core_Model_Website $website */
            $groupCollection = $website->getGroupCollection();
            $websiteId = $website->getId();
            $data[$websiteId] = [
                'object' => $website,
                'storeGroups' => [],
                'count' => 0,
            ];
            $defaultGroupId = $website->getDefaultGroupId();
            foreach ($groupCollection as $storeGroup) {
                /** @var Mage_Core_Model_Store_Group $storeGroup */
                $storeCollection = $storeGroup->getStoreCollection();
                $storeGroupCount = max(1, $storeCollection->count());
                $data[$websiteId]['storeGroups'][$storeGroup->getId()] = [
                    'object' => $storeGroup,
                    'stores' => [],
                    'count' => $storeGroupCount,
                ];
                $data[$websiteId]['count'] += $storeGroupCount;
                if ($storeGroup->getId() == $defaultGroupId) {
                    $storeGroup->setData('is_default', true);
                }
                $defaultStoreId = $storeGroup->getDefaultStoreId();
                foreach ($storeCollection as $store) {
                    /** @var Mage_Core_Model_Store $store */
                    $data[$websiteId]['storeGroups'][$storeGroup->getId()]['stores'][$store->getId()] = [
                        'object' => $store,
                    ];
                    if ($store->getId() == $defaultStoreId) {
                        $store->setData('is_default', true);
                    }
                }
            }

            $data[$websiteId]['count'] = max(1, $data[$websiteId]['count']);
        }
        return $data;
    }

    /**
     * Create new cell template
     *
     * @return Mage_Adminhtml_Block_Template
     */
    protected function _createCellTemplate()
    {
        return clone($this->_cellTemplate);
    }

    /**
     * Render website
     *
     * @return string
     */
    public function renderWebsite(Mage_Core_Model_Website $website)
    {
        return $this->_createCellTemplate()
            ->setObject($website)
            ->setLinkUrl($this->getUrl('*/*/editWebsite', ['website_id' => $website->getWebsiteId()]))
            ->setInfo($this->__('Code') . ': ' . $this->escapeHtml($website->getCode()))
            ->toHtml();
    }

    /**
     * Render store group
     *
     * @return string
     */
    public function renderStoreGroup(Mage_Core_Model_Store_Group $storeGroup)
    {
        $rootCategory = Mage::getModel('catalog/category')->load($storeGroup->getRootCategoryId());
        return $this->_createCellTemplate()
            ->setObject($storeGroup)
            ->setLinkUrl($this->getUrl('*/*/editGroup', ['group_id' => $storeGroup->getGroupId()]))
            ->setInfo($this->__('Root Category') . ': ' . $this->escapeHtml($rootCategory->getName()))
            ->toHtml();
    }

    /**
     * Render store
     *
     * @return string
     */
    public function renderStore(Mage_Core_Model_Store $store)
    {
        $cell = $this->_createCellTemplate()
            ->setObject($store)
            ->setLinkUrl($this->getUrl('*/*/editStore', ['store_id' => $store->getStoreId()]))
            ->setInfo($this->__('Code') . ': ' . $this->escapeHtml($store->getCode()));
        if (!$store->getIsActive()) {
            $cell->setClass('strike');
        }
        return $cell->toHtml();
    }
}
