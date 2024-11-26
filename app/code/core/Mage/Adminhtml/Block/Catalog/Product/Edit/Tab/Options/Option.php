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
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Widget
{
    protected $_product;

    protected $_productInstance;

    protected $_values;

    protected $_itemCount = 1;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/options/option.phtml');
        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    public function getItemCount()
    {
        return $this->_itemCount;
    }

    public function setItemCount($itemCount)
    {
        $this->_itemCount = max($this->_itemCount, $itemCount);
        return $this;
    }

    /**
     * Get Product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($product = Mage::registry('product')) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = Mage::getSingleton('catalog/product');
            }
        }

        return $this->_productInstance;
    }

    public function setProduct($product)
    {
        $this->_productInstance = $product;
        return $this;
    }

    /**
     * Retrieve options field name prefix
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'product[options]';
    }

    /**
     * Retrieve options field id prefix
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'product_option';
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getOptionsReadonly();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('catalog')->__('Delete Option'),
                    'class' => 'delete delete-product-option '
                ])
        );

        $path = 'global/catalog/product/options/custom/groups';

        foreach (Mage::getConfig()->getNode($path)->children() as $group) {
            $this->setChild(
                $group->getName() . '_option_type',
                $this->getLayout()->createBlock(
                    (string) Mage::getConfig()->getNode($path . '/' . $group->getName() . '/render')
                )
            );
        }

        return parent::_prepareLayout();
    }

    public function getAddButtonId()
    {
        return $this->getLayout()
                ->getBlock('admin.product.options')
                ->getChild('add_button')->getId();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData([
                'id' => $this->getFieldId() . '_{{id}}_type',
                'class' => 'select select-product-option-type required-option-select'
            ])
            ->setName($this->getFieldName() . '[{{id}}][type]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_product_options_type')->toOptionArray());

        return $select->getHtml();
    }

    public function getRequireSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData([
                'id' => $this->getFieldId() . '_{{id}}_is_require',
                'class' => 'select'
            ])
            ->setName($this->getFieldName() . '[{{id}}][is_require]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $canEditPrice = $this->getCanEditPrice();
        $canReadPrice = $this->getCanReadPrice();
        $this->getChild('select_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChild('file_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChild('date_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChild('text_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        return $this->getChildHtml('text_option_type') . "\n" .
            $this->getChildHtml('file_option_type') . "\n" .
            $this->getChildHtml('select_option_type') . "\n" .
            $this->getChildHtml('date_option_type');
    }

    public function getOptionValues()
    {
        $optionsArr = array_reverse($this->getProduct()->getOptions(), true);

        if (!$this->_values) {
            $showPrice = $this->getCanReadPrice();
            $values = [];
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            foreach ($optionsArr as $option) {
                /** @var Mage_Catalog_Model_Product_Option $option */

                $this->setItemCount($option->getOptionId());

                $value = [];

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $this->escapeHtml($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['can_edit_price'] = $this->getCanEditPrice();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                        $option->getOptionId(),
                        'title',
                        is_null($option->getStoreTitle())
                    );
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle()) ? 'disabled' : null;
                }

                if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $optionValue) {
                        /** @var Mage_Catalog_Model_Product_Option_Value $optionValue */
                        $value['optionValues'][$i] = [
                            'item_count' => max($itemCount, $optionValue->getOptionTypeId()),
                            'option_id' => $optionValue->getOptionId(),
                            'option_type_id' => $optionValue->getOptionTypeId(),
                            'title' => $this->escapeHtml($optionValue->getTitle()),
                            'price' => ($showPrice)
                                ? $this->getPriceValue($optionValue->getPrice(), $optionValue->getPriceType()) : '',
                            'price_type' => ($showPrice) ? $optionValue->getPriceType() : 0,
                            'sku' => $this->escapeHtml($optionValue->getSku()),
                            'sort_order' => $optionValue->getSortOrder(),
                        ];

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                $optionValue->getOptionId(),
                                'title',
                                is_null($optionValue->getStoreTitle()),
                                $optionValue->getOptionTypeId()
                            );
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($optionValue->getStoreTitle())
                                ? 'disabled' : null;
                            if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                    $optionValue->getOptionId(),
                                    'price',
                                    is_null($optionValue->getstorePrice()),
                                    $optionValue->getOptionTypeId()
                                );
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($optionValue->getStorePrice())
                                    ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = ($showPrice)
                        ? $this->getPriceValue($option->getPrice(), $option->getPriceType()) : '';
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->escapeHtml($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $this->escapeHtml($option->getFileExtension());
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0' &&
                        $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE
                    ) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                            $option->getOptionId(),
                            'price',
                            is_null($option->getStorePrice())
                        );
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice()) ? 'disabled' : null;
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }

    /**
     * Retrieve html of scope checkbox
     *
     * @param string $id
     * @param string $name
     * @param bool $checked
     * @param string $selectId
     * @return string
     */
    public function getCheckboxScopeHtml($id, $name, $checked = true, $selectId = '-1')
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }
        $selectNameHtml = '';
        $selectIdHtml = '';
        if ($selectId != '-1') {
            $selectNameHtml = '[values][' . $selectId . ']';
            $selectIdHtml = 'select_' . $selectId . '_';
        }
        $checkbox = '<input type="checkbox" id="' . $this->getFieldId() . '_' . $id . '_' .
            $selectIdHtml . $name . '_use_default" class="product-option-scope-checkbox" name="' .
            $this->getFieldName() . '[' . $id . ']' . $selectNameHtml . '[scope][' . $name . ']" value="1" ' .
            $checkedHtml . '/>';
        $checkbox .= '<label class="normal" for="' . $this->getFieldId() . '_' . $id . '_' .
            $selectIdHtml . $name . '_use_default">' . $this->__('Use Default Value') . '</label>';
        return $checkbox;
    }

    public function getPriceValue($value, $type)
    {
        if ($type == 'percent') {
            return number_format($value, 2, null, '');
        } elseif ($type == 'fixed') {
            return number_format($value, 2, null, '');
        }
    }
}
