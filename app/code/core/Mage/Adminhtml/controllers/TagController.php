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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tags'), Mage::helper('adminhtml')->__('Tags'))
        ;
        return $this;
    }

    /**
     * Prepare tag model for manipulation
     *
     * @return Mage_Tag_Model_Tag | false
     */
    protected function _initTag()
    {
        $id = $this->getRequest()->getParam('tag_id');
        $storeId = $this->getRequest()->getParam('store');
        $model = Mage::getModel('tag/tag');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                return false;
            }
            $model->setStoreId($storeId);
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
        /**
         * setting status parameter for grid filter for non-ajax request
         *
         */
        if ($this->getRequest()->getParam('pending') && !$this->getRequest()->getParam('isAjax')) {
            $this->getRequest()->setParam('filter', base64_encode('status=' . Mage_Tag_Model_Tag::STATUS_PENDING));
        }
        elseif (!$this->getRequest()->getParam('isAjax')) {
            $this->getRequest()->setParam('filter', '');
        }

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('All Tags'), Mage::helper('adminhtml')->__('All Tags'))
            ->_setActiveMenu('catalog/tag/all')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_tag'))
            ->renderLayout();
    }

    /**
     * Action to draw grid loaded by ajax
     *
     */
    public function ajaxGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_tag_grid')->toHtml()
        );
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
        if (0 === (int)$this->getRequest()->getParam('store')) {
            $this->_redirect('*/*/*/', array('store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true));
            return;
        }

        if (!$model = $this->_initTag()) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Wrong Tag specified')
            );
            $this->_redirect('*/*/index', array(
                'store' => $this->getRequest()->getParam('store')
            ));
            return;
        }

        $model->addSummary($this->getRequest()->getParam('store'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

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
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('adminhtml')->__('Wrong Tag specified')
                );
                $this->_redirect('*/*/index', array(
                    'store' => $data['store']
                ));
                return;
            }
            $model->addData($data);

            if (isset($postData['tag_assigned_products'])) {
                $productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput($postData['tag_assigned_products']);
                $tagRelationModel = Mage::getModel('tag/tag_relation');
                $tagRelationModel->addRelations($model, $productIds);
            }

            switch( $this->getRequest()->getParam('ret') ) {
                case 'all':
                    $url = $this->getUrl('*/*/index', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                case 'pending':
                    $url = $this->getUrl('*/tag/pending', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                default:
                    $url = $this->getUrl('*/*/index', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
            }

            try {
                $model->save();
                $model->aggregate();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tag was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);

                if ($this->getRequest()->getParam('ret') == 'edit') {
                    $url = $this->getUrl('*/tag/edit', array(
                        'tag_id'    => $model->getId(),
                        'store'  => $model->getStoreId()
                    ));
                }

                $this->getResponse()->setRedirect($url);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);
                $this->_redirect('*/*/edit', array(
                    'tag_id' => $model->getId(),
                    'store'  => $model->getStoreId()
                ));
                return;
            }
        }
        $this->getResponse()->setRedirect($url);
    }

    /**
     * Delete tag action
     *
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('tag_id')) {

            switch( $this->getRequest()->getParam('ret') ) {
                case 'all':
                    $url = $this->getUrl('*/*/', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                case 'pending':
                    $url = $this->getUrl('*/tag/pending', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                default:
                    $url = $this->getUrl('*/*/', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
            }

            try {
                $model = $this->_initTag();
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tag was successfully deleted'));
                $this->getResponse()->setRedirect($url);
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find a tag to delete'));
        $this->getResponse()->setRedirect($url);
    }

    /**
     * Pending tags
     *
     */
    public function pendingAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Pending Tags'), Mage::helper('adminhtml')->__('Pending Tags'))
            ->_setActiveMenu('catalog/tag/pending')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_pending'))
            ->renderLayout();
    }

    /**
     * Assigned products (with serializer block)
     *
     */
    public function assignedAction()
    {
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
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_product_grid')->toHtml()
        );
    }

    /**
     * Customers
     *
     */
    public function customerAction()
    {
        $this->_initTag();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_customer_grid')->toHtml()
        );
    }

    /**
     * Massaction for removing tags
     *
     */
    public function massDeleteAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if(!is_array($tagIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s)'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')->load($tagId);
                    $tag->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/'.$ret);
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
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s)'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')
                        ->load($tagId)
                        ->setStatus($this->getRequest()->getParam('status'));
                     $tag->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($tagIds))
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
        switch ($this->getRequest()->getActionName()) {
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
