<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers groups grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected string $_eventPrefix = 'adminhtml_customer_group_grid';

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGroupGrid');
        $this->setDefaultSort('type');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/group_collection')
            ->addTaxClass();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('time', [
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'align' => 'right',
            'index' => 'customer_group_id',
        ]);

        $this->addColumn('type', [
            'header' => Mage::helper('customer')->__('Group Name'),
            'index' => 'customer_group_code',
        ]);

        $this->addColumn('class_name', [
            'header' => Mage::helper('customer')->__('Tax Class'),
            'index' => 'class_name',
            'width' => '200px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param  Mage_Customer_Model_Group $row
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
