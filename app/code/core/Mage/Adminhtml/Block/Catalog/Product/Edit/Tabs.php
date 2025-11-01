<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * admin product edit tabs
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Product Information'));
    }

    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add groups without attributes

                foreach ($attributes as $key => $attribute) {
                    if (!$attribute->getIsVisible()) {
                        unset($attributes[$key]);
                    }
                }

                if (count($attributes) == 0) {
                    continue;
                }

                $this->addTab('group_' . $group->getId(), [
                    'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock(
                        $this->getAttributeTabBlock(),
                        'adminhtml.catalog.product.edit.tab.attributes',
                    )->setGroup($group)
                            ->setGroupAttributes($attributes)
                            ->toHtml()),
                ]);
            }

            if ($this->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', [
                    'label'     => Mage::helper('catalog')->__('Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_inventory')->toHtml()),
                ]);
            }

            /**
             * Don't display website tab for single mode
             */
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addTab('websites', [
                    'label'     => Mage::helper('catalog')->__('Websites'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_websites')->toHtml()),
                ]);
            }

            $this->addTab('categories', [
                'label'     => Mage::helper('catalog')->__('Categories'),
                'url'       => $this->getUrl('*/*/categories', ['_current' => true]),
                'class'     => 'ajax',
            ]);

            $this->addTab('related', [
                'label'     => Mage::helper('catalog')->__('Related Products'),
                'url'       => $this->getUrl('*/*/related', ['_current' => true]),
                'class'     => 'ajax',
            ]);

            $this->addTab('upsell', [
                'label'     => Mage::helper('catalog')->__('Up-sells'),
                'url'       => $this->getUrl('*/*/upsell', ['_current' => true]),
                'class'     => 'ajax',
            ]);

            $this->addTab('crosssell', [
                'label'     => Mage::helper('catalog')->__('Cross-sells'),
                'url'       => $this->getUrl('*/*/crosssell', ['_current' => true]),
                'class'     => 'ajax',
            ]);

            $storeId = 0;
            if ($this->getRequest()->getParam('store')) {
                $storeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
            }

            $alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
            $alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');

            if (($alertPriceAllow || $alertStockAllow) && !$product->isGrouped()) {
                $this->addTab('productalert', [
                    'label'     => Mage::helper('catalog')->__('Product Alerts'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_alerts', 'admin.alerts.products')->toHtml()),
                ]);
            }

            if ($this->getRequest()->getParam('id', false)) {
                if ($this->isModuleEnabled('Mage_Review', 'catalog')) {
                    if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/reviews_ratings')) {
                        $this->addTab('reviews', [
                            'label' => Mage::helper('catalog')->__('Product Reviews'),
                            'url'   => $this->getUrl('*/*/reviews', ['_current' => true]),
                            'class' => 'ajax',
                        ]);
                    }
                }

                if ($this->isModuleEnabled('Mage_Tag', 'catalog')) {
                    if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/tag')) {
                        $this->addTab('tags', [
                            'label'     => Mage::helper('catalog')->__('Product Tags'),
                            'url'   => $this->getUrl('*/*/tagGrid', ['_current' => true]),
                            'class' => 'ajax',
                        ]);

                        $this->addTab('customers_tags', [
                            'label'     => Mage::helper('catalog')->__('Customers Tagged Product'),
                            'url'   => $this->getUrl('*/*/tagCustomerGrid', ['_current' => true]),
                            'class' => 'ajax',
                        ]);
                    }
                }
            }

            /**
             * Do not change this tab id
             * @see Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
             * @see Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs
             */
            if (!$product->isGrouped()) {
                $this->addTab('customer_options', [
                    'label' => Mage::helper('catalog')->__('Custom Options'),
                    'url'   => $this->getUrl('*/*/options', ['_current' => true]),
                    'class' => 'ajax',
                ]);
            }
        } else {
            $this->addTab('set', [
                'label'     => Mage::helper('catalog')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('adminhtml/catalog_product_edit_tab_settings')->toHtml()),
                'active'    => true,
            ]);
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!($this->getData('product') instanceof Mage_Catalog_Model_Product)) {
            $this->setData('product', Mage::registry('product'));
        }

        return $this->getData('product');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return null|string
     */
    public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }

        return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
