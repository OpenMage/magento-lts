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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Category_Edit_Form extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{
    public const BLOCK_TABS = 'tabs';

    /**
     * Additional buttons on category page
     *
     * @var array
     */
    protected $_additionalButtons = [];

    protected $_template = 'catalog/category/edit/form.phtml';

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $category   = $this->getCategory();
        $categoryId = (int) $category->getId(); // 0 when we create category, otherwise some value for editing category

        $this->setChild(
            self::BLOCK_TABS,
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );

        // Save button
        if (!$category->isReadonly()) {
            $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        }

        // Delete button
        if (!in_array($categoryId, $this->getRootIds()) && $category->isDeleteable()) {
            $this->setChild(self::BUTTON_DELETE, $this->getButtonDeleteBlock());
        }

        // Reset button
        if (!$category->isReadonly()) {
            $this->setChild(self::BUTTON_RESET, $this->getButtonResetBlock());
        }

        return parent::_prepareLayout();
    }

    public function getButtonDeleteBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_DELETE)
            ->setLabel(Mage::helper('catalog')->__('Delete Category'))
            ->setOnClick("categorySubmit('" . $this->getSaveUrl() . "', true)");
    }

    public function getButtonResetBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        $category   = $this->getCategory();
        $categoryId = (int) $category->getId(); // 0 when we create category, otherwise some value for editing category
        $resetPath  = $categoryId ? '*/*/edit' : '*/*/add';
        return parent::getButtonBlockByType(self::BUTTON_RESET)
            ->setOnClick("categoryReset('" . $this->getUrl($resetPath, ['_current' => true]) . "',true)")
            ->resetClass();
    }

    public function getButtonSaveBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        $category   = $this->getCategory();
        $categoryId = (int) $category->getId(); // 0 when we create category, otherwise some value for editing category
        return parent::getButtonBlockByType(self::BUTTON_SAVE)
            ->setLabel(Mage::helper('catalog')->__('Save Category'))
            ->setOnClick("categoryDelete('" . $this->getUrl('*/*/delete', ['_current' => true]) . "', true, {$categoryId})");
    }

    public function getStoreConfigurationUrl()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $params = [];

        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $params['website'] = $store->getWebsite()->getCode();
            $params['store']   = $store->getCode();
        }
        return $this->getUrl('*/system_store', $params);
    }

    public function getSaveButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml(self::BUTTON_SAVE);
        }
        return '';
    }

    public function getResetButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml(self::BUTTON_RESET);
        }
        return '';
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return $this
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild(
            $alias . '_button',
            parent::getButtonBlock()->addData($config)
        );
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     * @return $this
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }

        return $this;
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml(self::BLOCK_TABS);
    }

    public function getHeader()
    {
        if ($this->hasStoreRootCategory()) {
            if ($this->getCategoryId()) {
                return $this->getCategoryName();
            } else {
                $parentId = (int) $this->getRequest()->getParam('parent');
                if ($parentId && ($parentId != Mage_Catalog_Model_Category::TREE_ROOT_ID)) {
                    return Mage::helper('catalog')->__('New Subcategory');
                } else {
                    return Mage::helper('catalog')->__('New Root Category');
                }
            }
        }
        return Mage::helper('catalog')->__('Set Root Category for Store');
    }

    public function getDeleteUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @return string
     */
    public function getRefreshPathUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
        if (!empty($products)) {
            return Mage::helper('core')->jsonEncode($products);
        }
        return '{}';
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
