<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Adminhtml paypal settlement reports grid block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Retain filter parameters in session
     *
     * @var bool
     */
    protected $_saveParametersInSession = true;

    /**
     * Constructor
     * Set main configuration of grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('settlementGrid');
        $this->setUseAjax(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('paypal/report_settlement_row_collection');
        $this->setCollection($collection);
        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('row_id', 'desc');
        }
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $settlement = Mage::getSingleton('paypal/report_settlement');
        $this->addColumn('report_date', [
            'header'    => $settlement->getFieldLabel('report_date'),
            'index'     => 'report_date',
            'type'     => 'date',
        ]);
        $this->addColumn('account_id', [
            'header'    => $settlement->getFieldLabel('account_id'),
            'index'     => 'account_id',
        ]);
        $this->addColumn('transaction_id', [
            'header'    => $settlement->getFieldLabel('transaction_id'),
            'index'     => 'transaction_id',
        ]);
        $this->addColumn('invoice_id', [
            'header'    => $settlement->getFieldLabel('invoice_id'),
            'index'     => 'invoice_id',
        ]);
        $this->addColumn('paypal_reference_id', [
            'header'    => $settlement->getFieldLabel('paypal_reference_id'),
            'index'     => 'paypal_reference_id',
        ]);
        $this->addColumn('transaction_event_code', [
            'header'    => $settlement->getFieldLabel('transaction_event'),
            'index'     => 'transaction_event_code',
            'type'      => 'options',
            'options'   => Mage::getModel('paypal/report_settlement_row')->getTransactionEvents(),
        ]);
        $this->addColumn('transaction_initiation_date', [
            'header'    => $settlement->getFieldLabel('transaction_initiation_date'),
            'index'     => 'transaction_initiation_date',
            'type'      => 'datetime',
        ]);
        $this->addColumn('transaction_completion_date', [
            'header'    => $settlement->getFieldLabel('transaction_completion_date'),
            'index'     => 'transaction_completion_date',
            'type'      => 'datetime',
        ]);
        $this->addColumn('gross_transaction_amount', [
            'header'    => $settlement->getFieldLabel('gross_transaction_amount'),
            'index'     => 'gross_transaction_amount',
            'type'      => 'currency',
            'currency'  => 'gross_transaction_currency',
        ]);
        $this->addColumn('fee_amount', [
            'header'    => $settlement->getFieldLabel('fee_amount'),
            'index'     => 'fee_amount',
            'type'      => 'currency',
            'currency'  => 'gross_transaction_currency',
        ]);
        return parent::_prepareColumns();
    }

    /**
     * Return grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }

    /**
     * Return item view URL
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/*/details', ['id' => $item->getId()]);
    }
}
