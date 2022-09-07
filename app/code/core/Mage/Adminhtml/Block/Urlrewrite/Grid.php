<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml urlrewrite grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('urlrewriteGrid');
        $this->setDefaultSort('url_rewrite_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('core/url_rewrite_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('url_rewrite_id', [
            'header'    => $this->__('ID'),
            'width'     => '50px',
            'index'     => 'url_rewrite_id'
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'    => $this->__('Store View'),
                'width'     => '200px',
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view' => true,
            ]);
        }

        $this->addColumn('is_system', [
            'header'    =>$this->__('Type'),
            'width'     => '50px',
            'index'     => 'is_system',
            'type'      => 'options',
            'options'   => [
                1 => $this->__('System'),
                0 => $this->__('Custom')
            ],
        ]);

        $this->addColumn('id_path', [
            'header'    => $this->__('ID Path'),
            'width'     => '50px',
            'index'     => 'id_path'
        ]);
        $this->addColumn('request_path', [
            'header'    => $this->__('Request Path'),
            'width'     => '50px',
            'index'     => 'request_path'
        ]);
        $this->addColumn('target_path', [
            'header'    => $this->__('Target Path'),
            'width'     => '50px',
            'index'     => 'target_path'
        ]);
        $this->addColumn('options', [
            'header'    => $this->__('Options'),
            'width'     => '50px',
            'index'     => 'options'
        ]);
        $this->addColumn('actions', [
            'header'    => $this->__('Action'),
            'width'     => '15px',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'action',
            'actions'   => [
                [
                    'url'       => $this->getUrl('*/*/edit') . 'id/$url_rewrite_id',
                    'caption'   => $this->__('Edit'),
                ],
            ]
        ]);
        //$this->addExportType('*/*/exportCsv', $this->__('CSV'));
        //$this->addExportType('*/*/exportXml', $this->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id'=>$row->getId()]);
        //return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }
}

