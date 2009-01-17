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
 * Adminhtml urlrewrite product grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setRowClickCallback('urlrewrite.gridRowClick');
        $this->setUseAjax(true);
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('adminhtml')->__('ID'),
                'width' => '50px',
                'index' => 'entity_id',
        ));

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('adminhtml')->__('Name'),
                'index' => 'name',
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('adminhtml')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('adminhtml')->__('Status'),
                'width' => '50px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        /*
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('stores', array(
                'header'    => Mage::helper('adminhtml')->__('Store Views'),
                'type'      => 'store',
                'width'     => '100px',
                'sortable'  => false,
                'index'     => 'stores',
           ));
        }
        */
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/jsonProductInfo', array('id' => $row->getId()));
    }

}
