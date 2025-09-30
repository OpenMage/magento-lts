<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * New attribute panel on product edit page
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create extends Mage_Adminhtml_Block_Widget_Button
{
    /**
     * Config of create new attribute
     *
     * @var Varien_Object|null
     */
    protected $_config = null;

    /**
     * Retrieve config of new attribute creation
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->setId('create_attribute_' . $this->getConfig()->getGroupId())
            ->setOnClick($this->getJsObjectName() . '.create();')
            ->setType('button')
            ->setClass('add')
            ->setLabel(Mage::helper('adminhtml')->__('Create New Attribute'));

        $this->getConfig()
            ->setUrl($this->getUrl(
                '*/catalog_product_attribute/new',
                [
                    'group'     => $this->getConfig()->getGroupId(),
                    'tab'       => $this->getConfig()->getTabId(),
                    'store'     => $this->getConfig()->getStoreId(),
                    'product'   => $this->getConfig()->getProductId(),
                    'set'       => $this->getConfig()->getAttributeSetId(),
                    'type'      => $this->getConfig()->getTypeId(),
                    'popup'     => 1,
                ],
            ));

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setCanShow(true);
        Mage::dispatchEvent('adminhtml_catalog_product_edit_tab_attributes_create_html_before', ['block' => $this]);
        if (!$this->getCanShow()) {
            return '';
        }

        $html = parent::_toHtml();

        return $html . Mage::helper('adminhtml/js')->getScript(
            "var {$this->getJsObjectName()} = new Product.Attributes('{$this->getId()}');\n"
            . "{$this->getJsObjectName()}.setConfig(" . Mage::helper('core')->jsonEncode($this->getConfig()->getData()) . ");\n",
        );
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
}
