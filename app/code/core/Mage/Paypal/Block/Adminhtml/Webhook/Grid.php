<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Block_Adminhtml_Webhook_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('paypalWebhookGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    #[Override]
    protected function _prepareCollection(): self
    {
        $collection = Mage::getModel('paypal/webhook_event')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    #[Override]
    protected function _prepareColumns(): self
    {
        $this->addColumn('entity_id', [
            'header' => Mage::helper('paypal')->__('ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
            'width'  => '60px',
        ]);

        $this->addColumn('webhook_event_id', [
            'header' => Mage::helper('paypal')->__('Webhook Event ID'),
            'index'  => 'webhook_event_id',
        ]);

        $this->addColumn('event_type', [
            'header' => Mage::helper('paypal')->__('Event Type'),
            'index'  => 'event_type',
        ]);

        $this->addColumn('resource_id', [
            'header' => Mage::helper('paypal')->__('Resource ID'),
            'index'  => 'resource_id',
        ]);

        $this->addColumn('increment_id', [
            'header'   => Mage::helper('paypal')->__('Order #'),
            'index'    => 'increment_id',
            'renderer' => 'paypal/adminhtml_grid_renderer_order',
        ]);

        $this->addColumn('status', [
            'header'   => Mage::helper('paypal')->__('Status'),
            'index'    => 'status',
            'renderer' => 'paypal/adminhtml_webhook_grid_renderer_status',
        ]);

        $this->addColumn('processing_attempts', [
            'header' => Mage::helper('paypal')->__('Attempts'),
            'index'  => 'processing_attempts',
            'type'   => 'number',
            'width'  => '80px',
        ]);

        $this->addColumn('payload_json', [
            'header'   => Mage::helper('paypal')->__('Payload'),
            'index'    => 'payload_json',
            'renderer' => 'paypal/adminhtml_webhook_grid_renderer_payload',
            'width'    => '420px',
        ]);

        $this->addColumn('last_error', [
            'header' => Mage::helper('paypal')->__('Last Error'),
            'index'  => 'last_error',
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('paypal')->__('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ]);

        $this->addColumn('action', [
            'header'  => Mage::helper('paypal')->__('Action'),
            'type'    => 'action',
            'getter'  => 'getId',
            'actions' => [[
                'caption' => Mage::helper('paypal')->__('View'),
                'url'     => ['base' => '*/*/view'],
                'field'   => 'id',
            ]],
            'filter'  => false,
            'sortable' => false,
            'width'   => '80px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    #[Override]
    protected function _prepareMassaction(): self
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('webhook');

        $this->getMassactionBlock()->addItem('reprocess', [
            'label'   => Mage::helper('paypal')->__('Reprocess'),
            'url'     => $this->getUrl('*/*/massReprocess'),
            'confirm' => Mage::helper('paypal')->__('Queue selected webhook events for reprocessing?'),
        ]);

        $this->getMassactionBlock()->addItem('delete', [
            'label'   => Mage::helper('paypal')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('paypal')->__('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRowUrl($row): string
    {
        return $this->getUrl('*/*/view', ['id' => $row->getId()]);
    }
}
