<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions variable grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Variable_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsVariableGrid');
        $this->setDefaultSort('variable_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Admin_Model_Resource_Variable_Collection $collection */
        $collection = Mage::getResourceModel('admin/variable_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('variable_id', [
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'index'     => 'variable_id',
        ]);
        $this->addColumn('variable_name', [
            'header'    => Mage::helper('adminhtml')->__('Variable'),
            'index'     => 'variable_name',
        ]);
        $this->addColumn('is_allowed', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_allowed',
            'type'      => 'options',
            'options'   => [
                '1' => Mage::helper('adminhtml')->__('Allowed'),
                '0' => Mage::helper('adminhtml')->__('Not allowed')],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param  Mage_Admin_Model_Variable $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['variable_id' => $row->getId()]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/variableGrid', []);
    }
}
