<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */


class Mage_Paypal_Block_Adminhtml_Debug_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('paypalDebugGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection(): self
    {
        $collection = Mage::getModel('paypal/debug')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns(): self
    {
        $this->addColumn('entity_id', [
            'header' => Mage::helper('paypal')->__('ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
        ]);

        $this->addColumn('quote_id', [
            'header' => Mage::helper('paypal')->__('Quote #'),
            'index'  => 'quote_id',
            'type'   => 'number',
        ]);

        $this->addColumn('increment_id', [
            'header'    => Mage::helper('paypal')->__('Order #'),
            'index'     => 'increment_id',
            'renderer'  => 'paypal/adminhtml_grid_renderer_order',
        ]);

        $this->addColumn('action', [
            'header' => Mage::helper('paypal')->__('Action'),
            'index'  => 'action',
        ]);

        $this->addColumn('transaction_id', [
            'header' => Mage::helper('paypal')->__('Transaction ID'),
            'index'  => 'transaction_id',
        ]);

        $this->addColumn('request_body', [
            'header'   => Mage::helper('paypal')->__('Request'),
            'index'    => 'request_body',
            'renderer' => 'paypal/adminhtml_debug_grid_renderer_request',
            'width'    => '700px',
        ]);

        $this->addColumn('response_body', [
            'header'   => Mage::helper('paypal')->__('Response'),
            'index'    => 'response_body',
            'renderer' => 'paypal/adminhtml_debug_grid_renderer_response',
            'width'    => '700px',
        ]);

        $this->addColumn('exception_message', [
            'header'   => Mage::helper('paypal')->__('Exception'),
            'index'    => 'exception_message',
            'renderer' => 'paypal/adminhtml_debug_grid_renderer_message',
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('paypal')->__('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction(): self
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('debug');

        $this->getMassactionBlock()->addItem('delete', [
            'label'   => Mage::helper('paypal')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('paypal')->__('Are you sure?'),
        ]);

        return $this;
    }
}
