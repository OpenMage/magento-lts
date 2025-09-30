<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * roles grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Api_Grid_Role extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('roleGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('role_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel('api/roles')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('role_id', [
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'index'     => 'role_id',
            'align'     => 'right',
            'width'    => '50px',
        ]);

        $this->addColumn('role_name', [
            'header'    => Mage::helper('adminhtml')->__('Role Name'),
            'index'     => 'role_name',
        ]);

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/roleGrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/editrole', ['rid' => $row->getRoleId()]);
    }
}
