<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product options text type block
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method bool getSkipJsReloadPrice()
 */
class Mage_Catalog_Block_Product_View_Options_Type_Select extends Mage_Catalog_Block_Product_View_Options_Abstract
{
    /**
     * Return html for control element
     *
     * @return string|void
     */
    public function getValuesHtml()
    {
        $option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());
        $store = $this->getProduct()->getStore();

        if ($option->getType() === Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
            || $option->getType() === Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE
        ) {
            $require = ($option->getIsRequire()) ? ' required-entry' : '';
            $extraParams = '';
            /** @var Mage_Core_Block_Html_Select $block */
            $block = $this->getLayout()->createBlock('core/html_select');
            $select = $block->setData([
                'id' => 'select_' . $option->getId(),
                'class' => $require . ' product-custom-option',
            ]);
            if ($option->getType() === Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options[' . $option->getId() . ']')
                    ->addOption('', $this->__('-- Please Select --'));
            } else {
                $select->setName('options[' . $option->getId() . '][]');
                $select->setClass('multiselect' . $require . ' product-custom-option');
            }

            /** @var Mage_Core_Helper_Data $helper */
            $helper = $this->helper('core');

            foreach ($option->getValues() as $value) {
                $priceStr = $this->_formatPrice([
                    'is_percent'    => ($value->getPriceType() === 'percent'),
                    'pricing_value' => $value->getPrice(($value->getPriceType() === 'percent')),
                ], false);
                $select->addOption(
                    $value->getOptionTypeId(),
                    $value->getTitle() . ' ' . $priceStr . '',
                    ['price' => $helper::currencyByStore($value->getPrice(true), $store, false)],
                );
            }
            if ($option->getType() === Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                $extraParams .= ' onchange="opConfig.reloadPrice()"';
            }
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }

        if ($option->getType() === Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
            || $option->getType() === Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
        ) {
            $selectHtml = '<ul id="options-' . $option->getId() . '-list" class="options-list">';
            $require = ($option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
            switch ($option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    if (!$option->getIsRequire()) {
                        $selectHtml .= '<li><input type="radio" id="options_' . $option->getId() . '" class="'
                            . $class . ' product-custom-option" name="options[' . $option->getId() . ']"'
                            . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
                            . ' value="" checked="checked" /><span class="label"><label for="options_'
                            . $option->getId() . '">' . $this->__('None') . '</label></span></li>';
                    }
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($option->getValues() as $value) {
                $count++;

                $priceStr = $this->_formatPrice([
                    'is_percent'    => ($value->getPriceType() === 'percent'),
                    'pricing_value' => $value->getPrice($value->getPriceType() === 'percent'),
                ]);

                $htmlValue = $value->getOptionTypeId();
                if ($arraySign) {
                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }

                /** @var Mage_Core_Helper_Data $helper */
                $helper = $this->helper('core');

                $selectHtml .= '<li>' . '<input type="' . $type . '" class="' . $class . ' ' . $require
                    . ' product-custom-option"'
                    . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
                    . ' name="options[' . $option->getId() . ']' . $arraySign . '" id="options_' . $option->getId()
                    . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
                    . $helper::currencyByStore($value->getPrice(true), $store, false) . '" />'
                    . '<span class="label"><label for="options_' . $option->getId() . '_' . $count . '">'
                    . $this->escapeHtml($value->getTitle()) . ' ' . $priceStr . '</label></span>';
                if ($option->getIsRequire()) {
                    $selectHtml .= '<script type="text/javascript">' . '$(\'options_' . $option->getId() . '_'
                    . $count . '\').advaiceContainer = \'options-' . $option->getId() . '-container\';'
                    . '$(\'options_' . $option->getId() . '_' . $count
                    . '\').callbackFunction = \'validateOptionsCallback\';' . '</script>';
                }
                $selectHtml .= '</li>';
            }

            return $selectHtml . '</ul>';
        }
    }
}
