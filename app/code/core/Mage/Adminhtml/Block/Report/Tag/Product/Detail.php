<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tags detail for product report blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Tag_Product_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_tag_product_detail';
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
        $this->_headerText = Mage::helper('reports')->__('Tags submitted to %s', $this->escapeHtml($product->getName()));
        parent::__construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/product/'));
        $this->_addBackButton();
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
