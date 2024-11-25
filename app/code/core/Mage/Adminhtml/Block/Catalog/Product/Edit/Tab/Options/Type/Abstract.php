<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * customers defined options
 *
 * @category   Mage
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
