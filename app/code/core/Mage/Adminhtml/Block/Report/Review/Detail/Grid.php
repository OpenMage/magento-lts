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
 * Adminhtml report reviews product grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Review_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Report_Review_Detail_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviews_grid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/review_collection')
            ->addProductFilter((int)$this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn('nickname', [
            'header'    =>Mage::helper('reports')->__('Customer'),
            'width'     =>'100px',
            'index'     =>'nickname'
        ]);

        $this->addColumn('title', [
            'header'    =>Mage::helper('reports')->__('Title'),
            'width'     =>'150px',
            'index'     =>'title'
        ]);

        $this->addColumn('detail', [
            'header'    =>Mage::helper('reports')->__('Detail'),
            'index'     =>'detail'
        ]);

        $this->addColumn('created_at', [
            'header'    =>Mage::helper('reports')->__('Created At'),
            'index'     =>'created_at',
            'width'     =>'200px',
            'type'      =>'datetime'
        ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}

