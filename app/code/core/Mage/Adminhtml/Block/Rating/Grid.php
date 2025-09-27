<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Rating_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ratingsGrid');
        $this->setDefaultSort('rating_code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter(Mage::registry('entityId'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Rating Grid colunms
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rating_id', [
            'header'    => Mage::helper('rating')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'rating_id',
        ]);

        $this->addColumn('rating_code', [
            'header'    => Mage::helper('rating')->__('Rating Name'),
            'index'     => 'rating_code',
        ]);

        $this->addColumn('position', [
            'header' => Mage::helper('rating')->__('Sort Order'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'position',
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
