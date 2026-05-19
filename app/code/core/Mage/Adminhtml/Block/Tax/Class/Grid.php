<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tax class Grid
 *
 * @package    Mage_Adminhtml
 *
 * @method string getClassType()
 */
class Mage_Adminhtml_Block_Tax_Class_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected string $_eventPrefix = 'adminhtml_tax_class_grid';

    public function __construct()
    {
        parent::__construct();
        $this->setId('taxClassGrid');
        $this->setDefaultSort('class_name');
        $this->setDefaultDir('ASC');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tax/class')
            ->getCollection()
            ->setClassTypeFilter($this->getClassType());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    protected function _prepareColumns()
    {
        $this->addColumn(
            'class_name',
            [
                'header'    => Mage::helper('tax')->__('Class Name'),
                'align'     => 'left',
                'index'     => 'class_name',
            ],
        );

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     * @param  Mage_Tax_Model_Class $row
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
