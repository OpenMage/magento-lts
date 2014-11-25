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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml store tree
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $data = array();
        foreach (Mage::getModel('core/website')->getCollection() as $website) {
            /** @var $website Mage_Core_Model_Website */
            $groupCollection = $website->getGroupCollection();
            $data[$website->getId()] = array(
                'object' => $website,
                'storeGroups' => array(),
                'count' => 0
            );
            $defaultGroupId = $website->getDefaultGroupId();
            foreach ($groupCollection as $storeGroup) {
                /** @var $storeGroup Mage_Core_Model_Store_Group */
                $storeCollection = $storeGroup->getStoreCollection();
                $storeGroupCount = max(1, $storeCollection->count());
                $data[$website->getId()]['storeGroups'][$storeGroup->getId()] = array(
                    'object' => $storeGroup,
                    'stores' => array(),
                    'count' => $storeGroupCount
                );
                $data[$website->getId()]['count'] += $storeGroupCount;
                if ($storeGroup->getId() == $defaultGroupId) {
                    $storeGroup->setData('is_default', true);
                }
                $defaultStoreId = $storeGroup->getDefaultStoreId();
                foreach ($storeCollection as $store) {
                    /** @var $store Mage_Core_Model_Store */
                    $data[$website->getId()]['storeGroups'][$storeGroup->getId()]['stores'][$store->getId()] = array(
                        'object' => $store
                    );
                    if ($store->getId() == $defaultStoreId) {
                        $store->setData('is_default', true);
                    }
                }
            }

            $data[$website->getId()]['count'] = max(1, $data[$website->getId()]['count']);
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
     * @param Mage_Core_Model_Website $website
     * @return string
     */
    public function renderWebsite(Mage_Core_Model_Website $website)
    {
        return $this->_createCellTemplate()
            ->setObject($website)
            ->setLinkUrl($this->getUrl('*/*/editWebsite', array('website_id' => $website->getWebsiteId())))
            ->setInfo($this->__('Code') . ': ' . $this->escapeHtml($website->getCode()))
            ->toHtml();
    }

    /**
     * Render store group
     *
     * @param Mage_Core_Model_Store_Group $storeGroup
     * @return string
     */
    public function renderStoreGroup(Mage_Core_Model_Store_Group $storeGroup)
    {
        $rootCategory = Mage::getModel('catalog/category')->load($storeGroup->getRootCategoryId());
        return $this->_createCellTemplate()
            ->setObject($storeGroup)
            ->setLinkUrl($this->getUrl('*/*/editGroup', array('group_id' => $storeGroup->getGroupId())))
            ->setInfo($this->__('Root Category') . ': ' . $this->escapeHtml($rootCategory->getName()))
            ->toHtml();
    }

    /**
     * Render store
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function renderStore(Mage_Core_Model_Store $store)
    {
        $cell = $this->_createCellTemplate()
            ->setObject($store)
            ->setLinkUrl($this->getUrl('*/*/editStore', array('store_id' => $store->getStoreId())))
            ->setInfo($this->__('Code') . ': ' . $this->escapeHtml($store->getCode()));
        if (!$store->getIsActive()) {
            $cell->setClass('strike');
        }
        return $cell->toHtml();
    }

}
