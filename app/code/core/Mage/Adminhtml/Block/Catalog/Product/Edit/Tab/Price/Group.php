<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml group price item renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('catalog/product/edit/price/group.phtml');
    }

    /**
     * Sort values
     *
     * @param  array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, [$this, '_sortGroupPrices']);
        return $data;
    }

    /**
     * Sort group price values callback method
     *
     * @param  array $a
     * @param  array $b
     * @return int
     */
    protected function _sortGroupPrices($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }

        if ($a['cust_group'] != $b['cust_group']) {
            return $this->getCustomerGroups($a['cust_group']) < $this->getCustomerGroups($b['cust_group']) ? -1 : 1;
        }

        return 0;
    }

    /**
     * Prepare global layout
     *
     * Add "Add Group Price" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label' => Mage::helper('catalog')->__('Add Group Price'),
                'onclick' => 'return groupPriceControl.addItem()',
                'class' => 'add',
            ]);
        $button->setName('add_group_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    /**
     *  Get is percent flag
     *
     * @return int
     */
    public function getIsPercent()
    {
        return $this->getData('is_percent') ? $this->getData('is_percent') : 0;
    }
}
