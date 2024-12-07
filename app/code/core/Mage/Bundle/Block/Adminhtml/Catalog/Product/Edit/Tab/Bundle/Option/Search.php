<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle selection product block
 *
 * @category   Mage
 * @package    Mage_Bundle
 *
 * @method bool getFirstShow()
 * @method $this setFirstShow(bool $value)
 * @method string getIndex()
 * @method $this setIndex(string $value)
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search extends Mage_Adminhtml_Block_Widget
{
    protected function _construct()
    {
        $this->setId('bundle_option_selection_search');
        $this->setTemplate('bundle/product/edit/bundle/option/search.phtml');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('bundle')->__('Please Select Products to Add');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'bundle/adminhtml_catalog_product_edit_tab_bundle_option_search_grid',
                'adminhtml.catalog.product.edit.tab.bundle.option.search.grid'
            )
        );
        return parent::_prepareLayout();
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->getChild('grid')->setIndex($this->getIndex())
            ->setFirstShow($this->getFirstShow());

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'id'    => 'add_button_' . $this->getIndex(),
            'label' => Mage::helper('sales')->__('Add Selected Product(s) to Option'),
            'onclick' => 'bSelection.productGridAddSelected(event)',
            'class' => 'add',
        ];
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}
