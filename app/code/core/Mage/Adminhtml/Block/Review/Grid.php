<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml reviews grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviwGrid');
        $this->setDefaultSort('created_at');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
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

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
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
            ]);
        }

        $this->addColumn('type', [
            'header'    => Mage::helper('review')->__('Type'),
            'type'      => 'select',
            'index'     => 'type',
            'filter'    => 'adminhtml/review_grid_filter_type',
            'renderer'  => 'adminhtml/review_grid_renderer_type',
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('review')->__('Product Name'),
            'align'     => 'left',
            'type'      => 'text',
            'index'     => 'name',
            'escape'    => true,
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('review')->__('Product SKU'),
            'align'     => 'right',
            'type'      => 'text',
            'width'     => '50px',
            'index'     => 'sku',
            'escape'    => true,
        ]);

        $this->addColumn(
            'action',
            [
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
                                'ret'       => (Mage::registry('usePendingFilter')) ? 'pending' : null,
                            ],
                        ],
                        'field'   => 'id',
                    ],
                ],
            ],
        );

        if ($this->isModuleEnabled('Mage_Rss', 'catalog') &&
            Mage::helper('rss')->isRssAdminCatalogReviewEnabled()
        ) {
            $this->addRssList('rss/catalog/review', Mage::helper('catalog')->__('Pending Reviews RSS'));
        }

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->setMassactionIdFilter('rt.review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
            'label' => Mage::helper('review')->__('Delete'),
            'url'  => $this->getUrl(
                '*/*/massDelete',
                ['ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index'],
            ),
            'confirm' => Mage::helper('review')->__('Are you sure?'),
        ]);

        $statuses = Mage::helper('review')->getReviewStatusesOptionArray();
        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(MassAction::UPDATE_STATUS, [
            'label'         => Mage::helper('review')->__('Update Status'),
            'url'           => $this->getUrl(
                '*/*/massUpdateStatus',
                ['ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index'],
            ),
            'additional'    => [
                'status'    => [
                    'name'      => 'status',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('review')->__('Status'),
                    'values'    => $statuses,
                ],
            ],
        ]);
        return parent::_prepareMassaction();
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

    /**
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->getProductId() || $this->getCustomerId()) {
            return $this->getUrl(
                '*/catalog_product_review/' . (Mage::registry('usePendingFilter') ? 'pending' : ''),
                [
                    'productId' => $this->getProductId(),
                    'customerId' => $this->getCustomerId(),
                ],
            );
        }

        return $this->getCurrentUrl();
    }
}
