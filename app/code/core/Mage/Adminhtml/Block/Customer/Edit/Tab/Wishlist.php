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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Prepare customer wishlist product collection
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _prepareCollection()
    {
        $wishlist = Mage::getModel('wishlist/wishlist');
        $collection = $wishlist->loadByCustomer($this->_getCustomer())
            ->setSharedStoreIds($wishlist->getSharedStoreIds(false))
            ->getProductCollection()
                ->resetSortOrder()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
                ->setDaysInWishlist(true)
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

        /*$this->addColumn('product_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'index'     => 'product_id',
            'type'      => 'number',
            'width'     => '130px'
        ));*/

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('customer')->__('Product name'),
            'index'     => 'name'
        ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('customer')->__('User description'),
            'index'     => 'wishlist_item_description',
            'renderer'  => 'adminhtml/customer_edit_tab_wishlist_grid_renderer_description'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store', array(
                'header'    => Mage::helper('customer')->__('Added From'),
                'index'     => 'store_name',
                'type'      => 'store'
            ));
        }

        $this->addColumn('visible_in', array(
            'header'    => Mage::helper('customer')->__('Visible In'),
            'index'     => 'item_store_id',
            'type'      => 'store'
        ));

        $this->addColumn('added_at', array(
            'header'    => Mage::helper('customer')->__('Date Added'),
            'index'     => 'added_at',
            'gmtoffset' => true,
            'type'      => 'date'
        ));

        $this->addColumn('days', array(
            'header'    => Mage::helper('customer')->__('Days in Wishlist'),
            'index'     => 'days_in_wishlist',
            'type'      => 'number'
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('customer')->__('Action'),
            'index'     => 'wishlist_item_id',
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption' =>  Mage::helper('customer')->__('Delete'),
                    'url'     =>  '#',
                    'onclick' =>  'return wishlistControl.removeItem($wishlist_item_id);'
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
        if($column->getId()=='store') {
            $this->getCollection()->addFieldToFilter('item_store_id', $column->getFilter()->getCondition());
            return $this;
        }

        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addFieldToFilter($column->getIndex(), $column->getFilter()->getCondition());
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
}
