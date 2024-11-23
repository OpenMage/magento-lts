<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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
        $this->addColumn('entity_id', [
            'header' => $this->__('Entity ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
        ]);

        $this->addColumn('secret', [
            'header' => $this->__('Secret'),
            'index'  => 'secret',
        ]);

        $this->addColumn('redirect_uri', [
            'header' => $this->__('Redirect URI'),
            'index'  => 'redirect_uri',
        ]);

        $this->addColumn('grant_types', [
            'header' => $this->__('Grant Types'),
            'index'  => 'grant_types',
        ]);

        $this->addColumn('created_at', [
            'header' => $this->__('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ]);

        $this->addColumn('updated_at', [
            'header' => $this->__('Updated At'),
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
