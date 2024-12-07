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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml store grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @deprecated after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree
 */
class Mage_Adminhtml_Block_System_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('storeGrid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('core/website')
            ->getCollection()
            ->joinGroupAndStore();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('website_title', [
            'header'        => Mage::helper('core')->__('Website Name'),
            'align'         => 'left',
            'index'         => 'name',
            'filter_index'  => 'main_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_website'
        ]);

        $this->addColumn('group_title', [
            'header'        => Mage::helper('core')->__('Store Name'),
            'align'         => 'left',
            'index'         => 'group_title',
            'filter_index'  => 'group_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_group'
        ]);

        $this->addColumn('store_title', [
            'header'        => Mage::helper('core')->__('Store View Name'),
            'align'         => 'left',
            'index'         => 'store_title',
            'filter_index'  => 'store_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_store'
        ]);

        return parent::_prepareColumns();
    }
}
