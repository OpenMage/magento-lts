<?php

/**
 * OAuth2 Client Grid Block
 */
class Mage_Oauth2_Block_Adminhtml_Client_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var bool
     */
    protected $_editAllow = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('oauth2_client_grid')
            ->setDefaultSort('entity_id')
            ->setDefaultDir('DESC')
            ->setSaveParametersInSession(true);

        $this->_editAllow = Mage::getSingleton('admin/session')->isAllowed('system/oauth/consumer/edit');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Oauth2_Block_Adminhtml_Client_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('oauth2/client')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Oauth2_Block_Adminhtml_Client_Grid
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('oauth2');

        $this->addColumn('entity_id', [
            'header' => $helper->__('Entity ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
        ]);

        $this->addColumn('secret', [
            'header' => $helper->__('Secret'),
            'index'  => 'secret',
        ]);

        $this->addColumn('redirect_uri', [
            'header' => $helper->__('Redirect URI'),
            'index'  => 'redirect_uri',
        ]);

        $this->addColumn('grant_types', [
            'header' => $helper->__('Grant Types'),
            'index'  => 'grant_types',
        ]);

        $this->addColumn('created_at', [
            'header' => $helper->__('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ]);

        $this->addColumn('updated_at', [
            'header' => $helper->__('Updated At'),
            'index'  => 'updated_at',
            'type'   => 'datetime',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * Get row URL
     *
     * @param Mage_Core_Model_Abstract $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        return $this->_editAllow ? $this->getUrl('*/*/edit', ['id' => $row->getId()]) : null;
    }
}
