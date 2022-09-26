<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New product attribute created on product edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                ->setGroupAttributes($this->_getGroupAttributes())
        );

        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('catalog')->__('Close Window'),
                    'onclick' => 'addAttribute(true)'
                ])
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
        foreach($product->getAttributes($this->getRequest()->getParam('group')) as $attribute) {
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
            $this->getRequest()->getParam('tab') => $this->getChildHtml('attributes')
        ];

        return Mage::helper('core')->jsonEncode($result);
    }
}
