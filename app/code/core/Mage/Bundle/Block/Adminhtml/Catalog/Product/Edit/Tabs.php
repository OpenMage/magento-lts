<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Adminhtml product edit tabs
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected $_attributeTabBlock = 'bundle/adminhtml_catalog_product_edit_tab_attributes';

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('bundle_items', [
            'label'     => Mage::helper('bundle')->__('Bundle Items'),
            'url'   => $this->getUrl('*/*/bundles', ['_current' => true]),
            'class' => 'ajax',
        ]);
        $this->bindShadowTabs('bundle_items', 'customer_options');

        return $this;
    }
}
