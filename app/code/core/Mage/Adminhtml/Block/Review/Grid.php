<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml reviews grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviwGrid');
        $this->setDefaultSort('created_at');
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('review/review');
        $collection = $model->getProductCollection();

        if ($this->getProductId() || $this->getRequest()->getParam('productId', false)) {
            $productId = $this->getProductId();
            if (!$productId) {
                $productId = $this->getRequest()->getParam('productId');
            }
            $this->setProductId($productId);
            $collection->addEntityFilter($this->getProductId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId) {
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if (Mage::registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $collection->addStoreData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('review_id', [
            'header'        => Mage::helper('review')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'rt.review_id',
            'index'         => 'review_id',
        ]);

        $this->addColumn('created_at', [
            'header'        => Mage::helper('review')->__('Created On'),
            'align'         => 'left',
            'type'          => 'datetime',
            'filter_index'  => 'rt.created_at',
            'index'         => 'review_created_at',
        ]);

        if (!Mage::registry('usePendingFilter')) {
            $this->addColumn('status', [
                'header'        => Mage::helper('review')->__('Status'),
                'align'         => 'left',
                'type'          => 'options',
                'options'       => Mage::helper('review')->getReviewStatuses(),
                'width'         => '100px',
                'filter_index'  => 'rt.status_id',
                'index'         => 'status_id',
            ]);
        }

        $this->addColumn('title', [
            'header'        => Mage::helper('review')->__('Title'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.title',
            'index'         => 'title',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ]);

        $this->addColumn('nickname', [
            'header'        => Mage::helper('review')->__('Nickname'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.nickname',
            'index'         => 'nickname',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ]);

        $this->addColumn('detail', [
            'header'        => Mage::helper('review')->__('Review'),
            'align'         => 'left',
            'index'         => 'detail',
            'filter_index'  => 'rdt.detail',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ]);

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', [
                'header'    => Mage::helper('review')->__('Visible In'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
            ]);
        }

        $this->addColumn('type', [
            'header'    => Mage::helper('review')->__('Type'),
            'type'      => 'select',
            'index'     => 'type',
            'filter'    => 'adminhtml/review_grid_filter_type',
            'renderer'  => 'adminhtml/review_grid_renderer_type'
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('review')->__('Product Name'),
            'align'     => 'left',
            'type'      => 'text',
            'index'     => 'name',
            'escape'    => true
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('review')->__('Product SKU'),
            'align'     => 'right',
            'type'      => 'text',
            'width'     => '50px',
            'index'     => 'sku',
            'escape'    => true
        ]);

        $this->addColumn(
            'action',
            [
                'header'    => Mage::helper('adminhtml')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getReviewId',
                'actions'   => [
                    [
                        'caption' => Mage::helper('adminhtml')->__('Edit'),
                        'url'     => [
                            'base' => '*/catalog_product_review/edit',
                            'params' => [
                                'productId' => $this->getProductId(),
                                'customerId' => $this->getCustomerId(),
                                'ret'       => (Mage::registry('usePendingFilter')) ? 'pending' : null
                            ]
                        ],
                         'field'   => 'id'
                    ]
                ],
                'filter'    => false,
                'sortable'  => false
            ]
        );

        $this->addRssList('rss/catalog/review', Mage::helper('catalog')->__('Pending Reviews RSS'));

        return parent::_prepareColumns();
    }

    /**
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->setMassactionIdFilter('rt.review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('delete', [
            'label' => Mage::helper('review')->__('Delete'),
            'url'  => $this->getUrl(
                '*/*/massDelete',
                ['ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index']
            ),
            'confirm' => Mage::helper('review')->__('Are you sure?')
        ]);

        $statuses = Mage::helper('review')->getReviewStatusesOptionArray();
        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem('update_status', [
            'label'         => Mage::helper('review')->__('Update Status'),
            'url'           => $this->getUrl(
                '*/*/massUpdateStatus',
                ['ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index']
            ),
            'additional'    => [
                'status'    => [
                    'name'      => 'status',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('review')->__('Status'),
                    'values'    => $statuses
                ]
            ]
        ]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review/edit', [
            'id' => $row->getReviewId(),
            'productId' => $this->getProductId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => (Mage::registry('usePendingFilter')) ? 'pending' : null,
        ]);
    }

    public function getGridUrl()
    {
        if ($this->getProductId() || $this->getCustomerId()) {
            return $this->getUrl(
                '*/catalog_product_review/' . (Mage::registry('usePendingFilter') ? 'pending' : ''),
                [
                    'productId' => $this->getProductId(),
                    'customerId' => $this->getCustomerId(),
                ]
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
}
