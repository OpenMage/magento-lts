<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Custom Variable Grid Container
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Variable_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customVariablesGrid');
        $this->setDefaultSort('variable_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Core_Model_Resource_Variable_Collection $collection */
        $collection = Mage::getModel('core/variable')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('variable_id', [
            'header'    => Mage::helper('adminhtml')->__('Variable ID'),
            'width'     => '1',
            'index'     => 'variable_id',
        ]);

        $this->addColumn('code', [
            'header'    => Mage::helper('adminhtml')->__('Variable Code'),
            'index'     => 'code',
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('adminhtml')->__('Name'),
            'index'     => 'name',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['variable_id' => $row->getId()]);
    }
}
