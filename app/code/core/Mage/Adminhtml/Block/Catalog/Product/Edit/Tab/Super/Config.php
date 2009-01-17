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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog super product configurable tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/super/config.phtml');
        $this->setId('config_super_product');
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config_grid')
        );



        $this->setChild('create_empty',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Create Empty'),
                    'class' => 'add',
                    'onclick' => 'superProduct.createEmptyProduct()'
                ))
        );

        if ($this->_getProduct()->getId()) {
            $this->setChild('simple',
                $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config_simple')
            );

            $this->setChild('create_from_configurable',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('catalog')->__('Copy From Configurable'),
                        'class' => 'add',
                        'onclick' => 'superProduct.createNewProduct()'
                    ))
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getAttributesJson()
    {
        $attributes = $this->_getProduct()->getTypeInstance()->getConfigurableAttributesAsArray();
        if(!$attributes) {
            return '[]';
        }
        return Zend_Json::encode($attributes);
    }

    public function getLinksJson()
    {
        $products = $this->_getProduct()->getTypeInstance()->getUsedProducts();
        if(!$products) {
            return '{}';
        }
        $data = array();
        foreach ($products as $product) {
        	$data[$product->getId()] = $this->getConfigurableSettings($product);
        }
        return Zend_Json::encode($data);
    }

    public function getConfigurableSettings($product) {
        $data = array();
        foreach ($this->_getProduct()->getTypeInstance()->getUsedProductAttributes() as $attribute) {
            $data[] = array(
                'attribute_id' => $attribute->getId(),
                'label'        => $product->getAttributeText($attribute->getAttributeCode()),
                'value_index'  => $product->getData($attribute->getAttributeCode())
            );
        }

        return $data;
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    public function getGridJsObject()
    {
        return $this->getChild('grid')->getJsObjectName();
    }

    public function getNewEmptyProductUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array(
                'set'      => $this->_getProduct()->getAttributeSetId(),
                'type'     => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1
            )
        );
    }

    public function getNewProductUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array(
                'set'      => $this->_getProduct()->getAttributeSetId(),
                'type'     => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->_getProduct()->getId()
            )
        );
    }



    public function getQuickCreationUrl()
    {
        return $this->getUrl(
            '*/*/quickCreate',
            array(
                'product'  => $this->_getProduct()->getId()
            )
        );
    }


    protected function _getRequiredAttributesIds()
    {
        $attributesIds = array();
        foreach ($this->_getProduct()->getTypeInstance()->getConfigurableAttributes() as $attribute) {
            $attributesIds[] = $attribute->getProductAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    public function escapeJs($string)
    {
        return addcslashes($string, "'\r\n\\");
    }

}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config END