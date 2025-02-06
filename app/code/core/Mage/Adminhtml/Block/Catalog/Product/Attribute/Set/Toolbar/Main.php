<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml catalog product sets main page toolbar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Main extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/attribute/set/toolbar/main.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'addButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('catalog')->__('Add New Set'),
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/add')),
                    'class'     => 'add',
                ]),
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function getNewButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return Mage::helper('catalog')->__('Manage Attribute Sets');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_catalog_product_attribute_set_toolbar_main_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
