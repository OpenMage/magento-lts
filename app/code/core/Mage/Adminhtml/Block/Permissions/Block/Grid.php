<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions block grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsBlockGrid');
        $this->setDefaultSort('block_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('admin/block_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('block_id', [
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'index'     => 'block_id',
        ]);

        $this->addColumn('block_name', [
            'header'    => Mage::helper('adminhtml')->__('Block Name'),
            'index'     => 'block_name',
        ]);

        $this->addColumn('is_allowed', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_allowed',
            'type'      => 'options',
            'options'   => ['1' => Mage::helper('adminhtml')->__('Allowed'), '0' => Mage::helper('adminhtml')->__('Not allowed')],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Admin_Model_Block $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['block_id' => $row->getId()]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/blockGrid', []);
    }
}
