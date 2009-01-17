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
 * Adminhtml product downloads report grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Downloads_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('downloadsGrid');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        $collection = Mage::getResourceModel('reports/product_downloads_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('type_id', array(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE))
            ->addSummary();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'name'
        ));

        $this->addColumn('link_title', array(
            'header'    => Mage::helper('reports')->__('Link'),
            'index'     => 'link_title'
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('reports')->__('Product Sku'),
            'index'     =>'sku'
        ));

        $this->addColumn('purchases', array(
            'header'    => Mage::helper('reports')->__('Purchases'),
            'width'     => '215px',
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'purchases',
            'type'      => 'number'
        ));

        $this->addColumn('downloads', array(
            'header'    => Mage::helper('reports')->__('Downloads'),
            'width'     => '215px',
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'downloads',
            'type'      => 'number'
        ));

        $this->addExportType('*/*/exportDownloadsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportDownloadsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}
