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
 * Product after creation popup window
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Created extends Mage_Adminhtml_Block_Widget
{
    protected $_configurableProduct;
    protected $_product;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/created.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('catalog')->__('Close Window'),
                    'onclick' => 'addProduct(true)',
                ]),
        );
        return $this;
    }

    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getProductId()
    {
        return (int) $this->getRequest()->getParam('id');
    }

    /**
     * Identifies edit mode of popup
     *
     * @return bool
     */
    public function isEdit()
    {
        return (bool) $this->getRequest()->getParam('edit');
    }

    /**
     * Retrieve serialized json with configurable attributes values of simple
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $result = [];
        foreach ($this->getAttributes() as $attribute) {
            $value = $this->getProduct()->getAttributeText($attribute->getAttributeCode());

            $result[] = [
                'label'         => $value,
                'value_index'   => $this->getProduct()->getData($attribute->getAttributeCode()),
                'attribute_id'  => $attribute->getId(),
            ];
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    public function getAttributes()
    {
        if ($this->getConfigurableProduct()->getId()) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
            $productType = $this->getConfigurableProduct()->getTypeInstance(true);
            return $productType->getUsedProductAttributes($this->getConfigurableProduct());
        }

        $attributes = [];

        $attributesIds = $this->getRequest()->getParam('required');
        if ($attributesIds) {
            $attributesIds = explode(',', $attributesIds);
            foreach ($attributesIds as $attributeId) {
                $attribute = $this->getProduct()->getTypeInstance(true)->getAttributeById($attributeId, $this->getProduct());
                if (!$attribute) {
                    continue;
                }
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * Retrieve configurable product for created/edited simple
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getConfigurableProduct()
    {
        if (is_null($this->_configurableProduct)) {
            $this->_configurableProduct = Mage::getModel('catalog/product')
                ->setStore(0)
                ->load($this->getRequest()->getParam('product'));
        }
        return $this->_configurableProduct;
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (is_null($this->_product)) {
            $this->_product = Mage::getModel('catalog/product')
                ->setStore(0)
                ->load($this->getRequest()->getParam('id'));
        }
        return $this->_product;
    }
}
