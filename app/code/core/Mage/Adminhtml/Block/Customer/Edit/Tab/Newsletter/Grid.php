<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter queue grid block
 *
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
            'width'     => 10,
        ]);

        $this->addColumn('start_at', [
            'header'    => Mage::helper('customer')->__('Newsletter Start'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'queue_start_at',
            'default'   => ' ---- ',
        ]);

        $this->addColumn('finish_at', [
            'header'    => Mage::helper('customer')->__('Newsletter Finish'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'queue_finish_at',
            'gmtoffset' => true,
            'default'   => ' ---- ',
        ]);

        $this->addColumn('letter_sent_at', [
            'header'    => Mage::helper('customer')->__('Newsletter Received'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'letter_sent_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- ',
        ]);

        $this->addColumn('template_subject', [
            'header'    => Mage::helper('customer')->__('Subject'),
            'align'     => 'center',
            'index'     => 'template_subject',
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('customer')->__('Status'),
            'align'     => 'center',
            'filter'    => 'adminhtml/customer_edit_tab_newsletter_grid_filter_status',
            'index'     => 'queue_status',
            'renderer'  => 'adminhtml/customer_edit_tab_newsletter_grid_renderer_status',
        ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'align'     => 'center',
            'renderer'  => 'adminhtml/customer_edit_tab_newsletter_grid_renderer_action',
        ]);

        return parent::_prepareColumns();
    }
}
