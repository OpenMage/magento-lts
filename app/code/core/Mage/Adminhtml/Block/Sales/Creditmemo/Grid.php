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

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Creditmemo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_creditmemo_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_creditmemo_grid_collection';
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header'    => Mage::helper('sales')->__('Credit Memo #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ]);

        $this->addColumn('created_at', [
            'header'    => Mage::helper('sales')->__('Created At'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ]);

        $this->addColumn('order_increment_id', [
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
            'escape'    => true,
        ]);

        $this->addColumn('order_created_at', [
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ]);

        $this->addColumn('billing_name', [
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ]);

        $this->addColumn('state', [
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_creditmemo')->getStates(),
        ]);

        $this->addColumn('grand_total', [
            'header'    => Mage::helper('customer')->__('Refunded'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ]);

        $this->addColumn(
            'action',
            [
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => [
                    [
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => ['base' => '*/sales_creditmemo/view'],
                        'field'   => 'creditmemo_id'
                    ]
                ],
                'is_system' => true
            ]
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('creditmemo_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem(MassAction::PDF_CREDITMEMOS_ORDER, [
             'label' => Mage::helper('sales')->__('PDF Credit Memos'),
             'url'  => $this->getUrl('*/sales_creditmemo/pdfcreditmemos'),
        ]);

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $row
     * @return false|string
     */
    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/creditmemo')) {
            return false;
        }

        return $this->getUrl(
            '*/sales_creditmemo/view',
            [
                'creditmemo_id' => $row->getId(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true]);
    }
}
