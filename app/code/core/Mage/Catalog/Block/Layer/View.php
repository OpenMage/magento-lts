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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog layered navigation view block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Layer_View extends Mage_Core_Block_Template
{
    /**
     * Prepare child blocks
     *
     * @return Mage_Catalog_Block_Layer_View
     */
    public function _prepareLayout()
    {
        $this->setChild('layer_state', $this->getLayout()->createBlock('catalog/layer_state'));
        $this->setChild('category_filter', $this->getLayout()->createBlock('catalog/layer_filter_category')->init());


        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            $filterBlockName = 'catalog/layer_filter_attribute';
            if ($attribute->getFrontendInput() == 'price') {
                $filterBlockName = 'catalog/layer_filter_price';
            }

            $this->setChild($attribute->getAttributeCode().'_filter',
                $this->getLayout()->createBlock($filterBlockName)
                    ->setAttributeModel($attribute)
                    ->init());
        }
        Mage::getSingleton('catalog/layer')->apply();
        return parent::_prepareLayout();
    }

    /**
     * Get all fiterable attributes of current category
     *
     * @return array
     */
    protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $attributes = Mage::getSingleton('catalog/layer')->getFilterableAttributes();
            $this->setData('_filterable_attributes', $attributes);
        }
        return $attributes;
    }

    /**
     * Get layered navigation state html
     *
     * @return string
     */
    public function getStateHtml()
    {
        return $this->getChildHtml('layer_state');
    }

    /**
     * Retrieve filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array();
        if ($categoryFilter = $this->_getCategoryFilter()) {
            $filters[] = $categoryFilter;
        }

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            $filters[] = $this->getChild($attribute->getAttributeCode().'_filter');
        }

        return $filters;
    }

    protected function _getCategoryFilter()
    {
        return $this->getChild('category_filter');
    }

    protected function _getPriceFilter()
    {
        return $this->getChild('_price_filter');
    }

    public function canShowOptions()
    {
        foreach ($this->getFilters() as $filter) {
            if ($filter->getItemsCount()) {
                return true;
            }
        }
        return false;
    }

    public function canShowBlock()
    {
        return $this->canShowOptions() || count(Mage::getSingleton('catalog/layer')->getState()->getFilters());
    }
}
