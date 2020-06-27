<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product tags admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @return Mage_Tag_Model_Tag | false
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
     *
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
     *
     */
    public function ajaxGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action to draw pending tags grid loaded by ajax
     *
     */
    public function ajaxPendingGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * New tag action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit tag action
     *
     */
    public function editAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Tags'));

        if (! (int) $this->getRequest()->getParam('store')) {
            return $this->_redirect('*/*/*/', array('store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true));
        }

        if (! ($model = $this->_initTag())) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
            return $this->_redirect('*/*/index', array('store' => $this->getRequest()->getParam('store')));
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Tag'));

        Mage::register('tag_tag', $model);

        $this->_initAction()->renderLayout();
    }

    /**
     * Save tag action
     *
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($postData['tag_id'])) {
                $data['tag_id'] = $postData['tag_id'];
            }

            $data['name']               = trim($postData['tag_name']);
            $data['status']             = $postData['tag_status'];
            $data['base_popularity']    = (isset($postData['base_popularity'])) ? $postData['base_popularity'] : 0;
            $data['store']              = $postData['store_id'];

            if (!$model = $this->_initTag()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
                return $this->_redirect('*/*/index', array('store' => $data['store']));
            }

            $model->addData($data);

            if (isset($postData['tag_assigned_products'])) {
                $productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput(
                    $postData['tag_assigned_products']
                );
                $model->setData('tag_assigned_products', $productIds);
            }

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The tag has been saved.'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);

                if (($continue = $this->getRequest()->getParam('continue'))) {
                    return $this->_redirect('*/tag/edit', array('tag_id' => $model->getId(), 'store' => $model->getStoreId(), 'ret' => $continue));
                } else {
                    return $this->_redirect('*/tag/' . $this->getRequest()->getParam('ret', 'index'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);

                return $this->_redirect('*/*/edit', array('tag_id' => $model->getId(), 'store' => $model->getStoreId()));
            }
        }

        return $this->_redirect('*/tag/index', array('_current' => true));
    }

    /**
     * Delete tag action
     *
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
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        } else {
            $session->addError(Mage::helper('adminhtml')->__('Unable to find a tag to delete.'));
        }

        $this->getResponse()->setRedirect($this->getUrl('*/tag/' . $this->getRequest()->getParam('ret', 'index')));
    }

    /**
     * Pending tags
     *
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
     *
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
     *
     */
    public function assignedGridOnlyAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Tagged products
     *
     */
    public function productAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customers
     *
     */
    public function customerAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Massaction for removing tags
     *
     */
    public function massDeleteAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if(!is_array($tagIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')->load($tagId);
                    $tag->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }

    /**
     * Massaction for changing status of selected tags
     *
     */
    public function massStatusAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if(!is_array($tagIds)) {
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
                    $this->__('Total of %d record(s) have been updated.', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/'.$ret);
    }

    /**
     * Check currently called action by permissions for current user
     *
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'pending':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/pending');
                break;
            case 'all':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/all');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag');
                break;
        }
    }
}
