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
 * New product attribute created on product edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created extends Mage_Adminhtml_Block_Widget
{
    public const BLOCK_ATTRIBUTES = 'attributes';

    protected $_template = 'catalog/product/attribute/new/created.phtml';

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_ATTRIBUTES,
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_new_product_attributes')
                ->setGroupAttributes($this->_getGroupAttributes())
        );

        $this->addButtons();
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_CLOSE, $this->getButtonCloseBlock());
    }

    public function getButtonCloseBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_CLOSE)
            ->setOnClick('addAttribute(true)');
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getGroupAttributes()
    {
        $attributes = [];
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::registry('product');
        foreach ($product->getAttributes($this->getRequest()->getParam('group')) as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            if ($attribute->getId() == $this->getRequest()->getParam('attribute')) {
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAttributesBlockJson()
    {
        $result = [
            $this->getRequest()->getParam('tab') => $this->getChildHtml(self::BLOCK_ATTRIBUTES)
        ];

        return Mage::helper('core')->jsonEncode($result);
    }
}
