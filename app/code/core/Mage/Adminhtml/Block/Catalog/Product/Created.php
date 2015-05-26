<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product after creation popup window
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
                ->setData(array(
                    'label'   => Mage::helper('catalog')->__('Close Window'),
                    'onclick' => 'addProduct(true)'
                ))
        );
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
     * Indentifies edit mode of popup
     *
     * @return boolean
     */
    public function isEdit()
    {
        return (bool) $this->getRequest()->getParam('edit');
    }

    /**
     * Retrive serialized json with configurable attributes values of simple
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $result = array();
        foreach ($this->getAttributes() as $attribute) {
            $value = $this->getProduct()->getAttributeText($attribute->getAttributeCode());

            $result[] = array(
                'label'         => $value,
                'value_index'   => $this->getProduct()->getData($attribute->getAttributeCode()),
                'attribute_id'  => $attribute->getId()
            );
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    public function getAttributes()
    {
        if ($this->getConfigurableProduct()->getId()) {
            return $this->getConfigurableProduct()->getTypeInstance(true)->getUsedProductAttributes($this->getConfigurableProduct());
        }

        $attributes = array();

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
     * Retrive configurable product for created/edited simple
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
     * Retrive product
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
} // Class Mage_Adminhtml_Block_Catalog_Product_Created End
