<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * customers defined options
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/options/type/select.phtml');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'add_select_row_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('catalog')->__('Add New Row'),
                    'class' => 'add add-select-row',
                    'id'    => 'add_select_row_button_{{option_id}}',
                ]),
        );

        $this->setChild(
            'delete_select_row_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('catalog')->__('Delete Row'),
                    'class' => 'delete delete-select-row icon-btn',
                    'id'    => 'delete_select_row_button',
                ]),
        );

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_select_row_button');
    }

    public function getPriceTypeSelectHtml()
    {
        $this->getChild('option_price_type')
            ->setData('id', 'product_option_{{id}}_select_{{select_id}}_price_type')
            ->setName('product[options][{{id}}][values][{{select_id}}][price_type]');

        return parent::getPriceTypeSelectHtml();
    }
}
