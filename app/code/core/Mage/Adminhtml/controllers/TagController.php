<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product tags admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/tag')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tags'), Mage::helper('adminhtml')->__('Tags'));

        return $this;
    }

    /**
     * Prepare tag model for manipulation
     *
     * @return false|Mage_Tag_Model_Tag
     */
    protected function _initTag()
    {
        $model = Mage::getModel('tag/tag');
        $storeId = $this->getRequest()->getParam('store');
        $model->setStoreId($storeId);

        if (($id = $this->getRequest()->getParam('tag_id'))) {
            $model->setAddBasePopularity();
            $model->load($id);
            $model->setStoreId($storeId);

            if (!$model->getId()) {
                return false;
            }
        }

        Mage::register('current_tag', $model);
        return $model;
    }

    /**
     * Show grid action
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Tags'))
             ->_title($this->__('All Tags'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('All Tags'), Mage::helper('adminhtml')->__('All Tags'))
            ->_setActiveMenu('catalog/tag/all')
            ->renderLayout();
    }

    /**
     * Action to draw grid loaded by ajax
     * @return void
     */
    public function ajaxGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action to draw pending tags grid loaded by ajax
     * @return void
     */
    public function ajaxPendingGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * New tag action
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit tag action
     * @return $this|void
     */
    public function editAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Tags'));

        if (!(int) $this->getRequest()->getParam('store')) {
            return $this->_redirect('*/*/*/', ['store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true]);
        }

        if (!($model = $this->_initTag())) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
            return $this->_redirect('*/*/index', ['store' => $this->getRequest()->getParam('store')]);
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Tag'));

        Mage::register('tag_tag', $model);

        $this->_initAction()->renderLayout();
    }

    /**
     * Save tag action
     * @return $this|void
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($postData['tag_id'])) {
                $data['tag_id'] = $postData['tag_id'];
            }

            $data['name']               = trim($postData['tag_name']);
            $data['status']             = $postData['tag_status'];
            $data['base_popularity']    = $postData['base_popularity'] ?? 0;
            $data['store']              = $postData['store_id'];

            if (!$model = $this->_initTag()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
                return $this->_redirect('*/*/index', ['store' => $data['store']]);
            }

            $model->addData($data);

            if (isset($postData['tag_assigned_products'])) {
                $productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput(
                    $postData['tag_assigned_products'],
                );
                $model->setData('tag_assigned_products', $productIds);
            }

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The tag has been saved.'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);

                if (($continue = $this->getRequest()->getParam('continue'))) {
                    return $this->_redirect('*/tag/edit', ['tag_id' => $model->getId(), 'store' => $model->getStoreId(), 'ret' => $continue]);
                }

                return $this->_redirect('*/tag/' . $this->getRequest()->getParam('ret', 'index'));
            } catch (Exception $exception) {
                Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);

                return $this->_redirect('*/*/edit', ['tag_id' => $model->getId(), 'store' => $model->getStoreId()]);
            }
        }

        return $this->_redirect('*/tag/index', ['_current' => true]);
    }

    /**
     * Delete tag action
     * @return void
     */
    public function deleteAction()
    {
        $model   = $this->_initTag();
        $session = Mage::getSingleton('adminhtml/session');

        if ($model && $model->getId()) {
            try {
                $model->delete();
                $session->addSuccess(Mage::helper('adminhtml')->__('The tag has been deleted.'));
            } catch (Exception $exception) {
                $session->addError($exception->getMessage());
            }
        } else {
            $session->addError(Mage::helper('adminhtml')->__('Unable to find a tag to delete.'));
        }

        $this->getResponse()->setRedirect($this->getUrl('*/tag/' . $this->getRequest()->getParam('ret', 'index')));
    }

    /**
     * Pending tags
     * @return void
     */
    public function pendingAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Pending Tags'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Pending Tags'), Mage::helper('adminhtml')->__('Pending Tags'))
            ->_setActiveMenu('catalog/tag/pending')
            ->renderLayout();
    }

    /**
     * Assigned products (with serializer block)
     * @return void
     */
    public function assignedAction()
    {
        $this->_title($this->__('Tags'))->_title($this->__('Assigned'));

        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Assigned products grid
     * @return void
     */
    public function assignedGridOnlyAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Tagged products
     * @return void
     */
    public function productAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customers
     * @return void
     */
    public function customerAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Massaction for removing tags
     * @return void
     */
    public function massDeleteAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if (!is_array($tagIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')->load($tagId);
                    $tag->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($tagIds)),
                );
            } catch (Exception $exception) {
                Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }

    /**
     * Massaction for changing status of selected tags
     * @return void
     */
    public function massStatusAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if (!is_array($tagIds)) {
            // No products selected
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')
                        ->load($tagId)
                        ->setStatus($this->getRequest()->getParam('status'));
                    $tag->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($tagIds)),
                );
            } catch (Exception $exception) {
                Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
            }
        }

        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/' . $ret);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $session = Mage::getSingleton('admin/session');
        $action = strtolower($this->getRequest()->getActionName());
        return match ($action) {
            'index', 'ajaxgrid' => $session->isAllowed('catalog/tag/all'),
            'pending', 'ajaxpendinggrid' => $session->isAllowed('catalog/tag/pending'),
            // Shared by both grids: All Tags covers every tag, Pending Tags only tags awaiting approval
            'new', 'edit', 'save', 'delete', 'assigned', 'assignedgridonly', 'product', 'customer',
            'massdelete', 'massstatus' => $session->isAllowed('catalog/tag/all')
                || ($session->isAllowed('catalog/tag/pending') && $this->_isPendingTagRequest()),
            default => $session->isAllowed('catalog/tag'),
        };
    }

    /**
     * Whether the request only touches existing tags that are awaiting approval
     */
    protected function _isPendingTagRequest(): bool
    {
        $request = $this->getRequest();
        $tagIds = $request->getParam('tag');
        if (!is_array($tagIds)) {
            $tagIds = [$request->getParam('tag_id')];
        }

        $tagIds = array_unique(array_filter(array_map(intval(...), $tagIds), static fn(int $tagId): bool => $tagId > 0));
        if ($tagIds === []) {
            return false;
        }

        $statuses = $this->_getTagStatuses($tagIds);
        if (count($statuses) !== count($tagIds)) {
            return false;
        }

        foreach ($statuses as $status) {
            if ($status !== Mage_Tag_Model_Tag::STATUS_PENDING) {
                return false;
            }
        }

        return true;
    }

    /**
     * Status of each requested tag that exists, keyed by tag id
     *
     * @param  int[]           $tagIds
     * @return array<int, int>
     */
    protected function _getTagStatuses(array $tagIds): array
    {
        $collection = Mage::getResourceModel('tag/tag_collection')
            ->addFieldToFilter('main_table.tag_id', ['in' => $tagIds]);
        $statuses = [];
        foreach ($collection as $tag) {
            $statuses[(int) $tag->getId()] = (int) $tag->getStatus();
        }

        return $statuses;
    }
}
