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
 * Adminhtml permissions variable grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'sortable'  => true,
            'index'     => 'variable_id'
        ]);
        $this->addColumn('variable_name', [
            'header'    => Mage::helper('adminhtml')->__('Variable'),
            'index'     => 'variable_name'
        ]);
        $this->addColumn('is_allowed', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_allowed',
            'type'      => 'options',
            'options'   => [
                '1' => Mage::helper('adminhtml')->__('Allowed'),
                '0' => Mage::helper('adminhtml')->__('Not allowed')],
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Admin_Model_Variable $row
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
