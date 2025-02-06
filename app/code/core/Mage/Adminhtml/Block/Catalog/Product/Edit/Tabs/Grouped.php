<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * admin edit tabs for grouped product
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Grouped extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('super', [
            'label'     => Mage::helper('catalog')->__('Associated Products'),
            'url'       => $this->getUrl('*/*/superGroup', ['_current' => true]),
            'class'     => 'ajax',
        ]);
        return $this;
    }
}
