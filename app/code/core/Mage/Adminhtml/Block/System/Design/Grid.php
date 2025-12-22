<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Design changes grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Design_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('designGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid data collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        $collection = Mage::getResourceModel('core/design_collection');

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Define grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'        => Mage::helper('catalog')->__('Store'),
                'width'         => '100px',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false,
                'index'         => 'store_id',
            ]);
        }

        $this->addColumn('package', [
            'header'    => Mage::helper('catalog')->__('Design'),
            'width'     => '150px',
            'index'     => 'design',
        ]);
        $this->addColumn('date_from', [
            'header'    => Mage::helper('catalogrule')->__('Date From'),
            'align'     => 'left',
            'type'      => 'date',
            'index'     => 'date_from',
        ]);

        $this->addColumn('date_to', [
            'header'    => Mage::helper('catalogrule')->__('Date To'),
            'align'     => 'left',
            'type'      => 'date',
            'index'     => 'date_to',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Prepare row click url
     *
     * @param  Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /**
     * Prepare grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
