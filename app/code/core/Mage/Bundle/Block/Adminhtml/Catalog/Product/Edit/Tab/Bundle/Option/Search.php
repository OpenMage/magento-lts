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
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle selection product block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @return Mage_Adminhtml_Block_Widget
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
     * @return Mage_Adminhtml_Block_Widget
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
        $addButtonData = array(
            'id'    => 'add_button_' . $this->getIndex(),
            'label' => Mage::helper('sales')->__('Add Selected Product(s) to Option'),
            'onclick' => 'bSelection.productGridAddSelected(event)',
            'class' => 'add',
        );
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
