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
 * Adminhtml newsletter queue grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(Mage::helper('customer')->__('No Newsletter Found'));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/newsletter', ['_current' => true]);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('newsletter/queue_collection')
            ->addTemplateInfo()
            ->addSubscriberFilter(Mage::registry('subscriber')->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', [
            'header'    => Mage::helper('customer')->__('ID'),
            'align'     => 'left',
            'index'     => 'queue_id',
            'width'     => 10
        ]);

        $this->addColumn('start_at', [
            'header'    => Mage::helper('customer')->__('Newsletter Start'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'queue_start_at',
            'default'   => ' ---- '
        ]);

        $this->addColumn('finish_at', [
            'header'    => Mage::helper('customer')->__('Newsletter Finish'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'queue_finish_at',
            'gmtoffset' => true,
            'default'   => ' ---- '
        ]);

        $this->addColumn('letter_sent_at', [
            'header'    => Mage::helper('customer')->__('Newsletter Received'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'letter_sent_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ]);

        $this->addColumn('template_subject', [
            'header'    => Mage::helper('customer')->__('Subject'),
            'align'     => 'center',
            'index'     => 'template_subject'
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('customer')->__('Status'),
            'align'     => 'center',
            'filter'    => 'adminhtml/customer_edit_tab_newsletter_grid_filter_status',
            'index'     => 'queue_status',
            'renderer'  => 'adminhtml/customer_edit_tab_newsletter_grid_renderer_status'
         ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'align'     => 'center',
            'renderer'  => 'adminhtml/customer_edit_tab_newsletter_grid_renderer_action'
        ]);

        return parent::_prepareColumns();
    }
}
