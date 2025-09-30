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
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract extends Mage_Adminhtml_Block_Widget
{
    protected $_name = 'abstract';

    protected function _prepareLayout()
    {
        $this->setChild(
            'option_price_type',
            $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData([
                    'id' => 'product_option_{{option_id}}_price_type',
                    'class' => 'select product-option-price-type',
                ]),
        );

        $this->getChild('option_price_type')->setName('product[options][{{option_id}}][price_type]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_product_options_price')
            ->toOptionArray());

        return parent::_prepareLayout();
    }

    /**
     * Get html of Price Type select element
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        if ($this->getCanEditPrice() === false) {
            $this->getChild('option_price_type')->setExtraParams('disabled="disabled"');
        }
        return $this->getChildHtml('option_price_type');
    }
}
