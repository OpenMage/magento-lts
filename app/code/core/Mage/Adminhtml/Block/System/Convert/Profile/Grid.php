<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Convert profiles grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('convertProfileGrid');
        $this->setDefaultSort('profile_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dataflow/profile_collection')
            ->addFieldToFilter('entity_type', ['null' => '']);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('profile_id', [
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => '50px',
            'index'     => 'profile_id',
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('adminhtml')->__('Profile Name'),
            'index'     => 'name',
        ]);
        $this->addColumn('created_at', [
            'header'    => Mage::helper('adminhtml')->__('Created At'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     => 'created_at',
        ]);
        $this->addColumn('updated_at', [
            'header'    => Mage::helper('adminhtml')->__('Updated At'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     => 'updated_at',
        ]);

        $this->addColumn('action', [
            'align'     => 'center',
            'type'      => 'action',
            'actions'   => [
                [
                    'url'       => $this->getUrl('*/*/edit') . 'id/$profile_id',
                    'caption'   => Mage::helper('adminhtml')->__('Edit'),
                ],
            ],
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
