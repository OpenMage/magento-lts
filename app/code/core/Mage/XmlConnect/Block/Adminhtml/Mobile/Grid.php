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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application grid block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Xmlconnect_Block_Adminhtml_Mobile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('mobile_apps_grid');
        $this->setDefaultSort('application_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Initialize grid data collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xmlconnect/application')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Declare grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => $this->__('App Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('code', array(
            'header'    => $this->__('App Code'),
            'align'     => 'left',
            'index'     => 'code',
            'width'     => '200',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => $this->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false,
                'width'         => '250',
            ));
        }

        $this->addColumn('type', array(
            'header'    => $this->__('Device'),
            'type'      => 'text',
            'index'     => 'type',
            'align'     => 'center',
            'filter'    => 'adminhtml/widget_grid_column_filter_select',
            'options'   => Mage::helper('xmlconnect')->getSupportedDevices(),
            'renderer'  => 'xmlconnect/adminhtml_mobile_grid_renderer_type',
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'index'     => 'status',
            'renderer'  => 'xmlconnect/adminhtml_mobile_grid_renderer_bool',
            'align'     => 'center',
            'filter'    => 'adminhtml/widget_grid_column_filter_select',
            'options'   => Mage::helper('xmlconnect')->getStatusOptions(),

        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('application_id' => $row->getId()));
    }
}
