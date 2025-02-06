<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * admin edit tabs for configurable products
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
        $this->addTab('super_settings', [
            'label'     => Mage::helper('catalog')->__('Configurable Product Settings'),
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_settings')->toHtml(),
            'active'    => true,
        ]);

        return $this;
    }
}
