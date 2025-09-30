<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * New product attribute created on product edit page
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created extends Mage_Adminhtml_Block_Widget
{
    /**
     * Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/attribute/new/created.phtml');
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'attributes',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_new_product_attributes')
                ->setGroupAttributes($this->_getGroupAttributes()),
        );

        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('catalog')->__('Close Window'),
                    'onclick' => 'addAttribute(true)',
                ]),
        );
        return $this;
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
     */
    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAttributesBlockJson()
    {
        $result = [
            $this->getRequest()->getParam('tab') => $this->getChildHtml('attributes'),
        ];

        return Mage::helper('core')->jsonEncode($result);
    }
}
