<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Variable Grid Container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Variable_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     *
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
