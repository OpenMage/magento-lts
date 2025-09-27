<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * admin edit tabs for grouped product
 *
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
