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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml customer orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Default sort field
     *
     * @var string
     */

    protected $_defaultSort = 'added_at';

    /**
     * Parent template name
     *
     * @var string
     */
    protected $_parentTemplate;

    /**
     * List of helpers to show options for product cells
     */
    protected $_productHelpers = array();

    /**
     * Initialize Grid
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('wishlistGrid');
        $this->setUseAjax(true);
        $this->_parentTemplate = $this->getTemplate();
        $this->setTemplate('customer/tab/wishlist.phtml');
        $this->setEmptyText(Mage::helper('customer')->__('No Items Found'));
        $this->addProductConfigurationHelper('default', 'catalog/product_configuration');
    }

    /**
     * Retrieve current customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Create customer wishlist item collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createCollection()
    {
        return Mage::getModel('wishlist/item')->getCollection()
            ->setWebsiteId($this->_getCustomer()->getWebsiteId())
            ->setCustomerGroupId($this->_getCustomer()->getGroupId());
    }

    /**
     * Prepare customer wishlist product collection
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _prepareCollection()
    {
        $collection = $this->_createCollection()->addCustomerIdFilter($this->_getCustomer()->getId())
            ->resetSortOrder()
            ->addDaysInWishlist()
            ->addStoreData();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'index'     => 'product_name',
            'renderer'  => 'adminhtml/customer_edit_tab_view_grid_renderer_item'
        ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('wishlist')->__('User Description'),
            'index'     => 'description',
            'renderer'  => 'adminhtml/customer_edit_tab_wishlist_grid_renderer_description'
        ));

        $this->addColumn('qty', array(
            'header'    => Mage::helper('catalog')->__('Qty'),
            'index'     => 'qty',
            'type'      => 'number',
            'width'     => '60px'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store', array(
                'header'    => Mage::helper('wishlist')->__('Added From'),
                'index'     => 'store_id',
                'type'      => 'store',
                'width'     => '160px'
            ));
        }

        $this->addColumn('added_at', array(
            'header'    => Mage::helper('wishlist')->__('Date Added'),
            'index'     => 'added_at',
            'gmtoffset' => true,
            'type'      => 'date'
        ));

        $this->addColumn('days', array(
            'header'    => Mage::helper('wishlist')->__('Days in Wishlist'),
            'index'     => 'days_in_wishlist',
            'type'      => 'number'
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('customer')->__('Action'),
            'index'     => 'wishlist_item_id',
            'renderer'  => 'adminhtml/customer_grid_renderer_multiaction',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('customer')->__('Configure'),
                    'url'       => 'javascript:void(0)',
                    'process'   => 'configurable',
                    'control_object' => 'wishlistControl'
                ),
                array(
                    'caption'   => Mage::helper('customer')->__('Delete'),
                    'url'       => '#',
                    'onclick'   => 'return wishlistControl.removeItem($wishlist_item_id);'
                )
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/wishlist', array('_current'=>true));
    }

    /**
     * Add column filter to collection
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _addColumnFilterToCollection($column)
    {
        /* @var $collection Mage_Wishlist_Model_Mysql4_Item_Collection */
        $collection = $this->getCollection();
        $value = $column->getFilter()->getValue();
        if ($collection && $value) {
            switch ($column->getId()) {
                case 'product_name':
                    $collection->addProductNameFilter($value);
                    break;
                case 'store':
                    $collection->addStoreFilter($value);
                    break;
                case 'days':
                    $collection->addDaysFilter($value);
                    break;
                default:
                    $collection->addFieldToFilter($column->getIndex(), $column->getFilter()->getCondition());
                    break;
            }
        }
        return $this;
    }

    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            switch ($column->getId()) {
                case 'product_name':
                    $collection->setOrderByProductName($column->getDir());
                    break;
                default:
                    parent::_setCollectionOrder($column);
                    break;
            }
        }
        return $this;
    }

    /**
     * Retrieve Grid Parent Block HTML
     *
     * @return string
     */
    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    /**
     * Retrieve Row click URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

    /**
     * Adds product type helper depended on product type (used to show options in item cell)
     *
     * @param string $productType
     * @param string $helperName
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    public function addProductConfigurationHelper($productType, $helperName)
    {
        $this->_productHelpers[$productType] = $helperName;
        return $this;
    }

    /**
     * Returns array of product configuration helpers
     *
     * @return array
     */
    public function getProductConfigurationHelpers()
    {
        return $this->_productHelpers;
    }
}
