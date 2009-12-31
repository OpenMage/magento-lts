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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tags'), Mage::helper('adminhtml')->__('Tags'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('All Tags'), Mage::helper('adminhtml')->__('All Tags'))
            ->_setActiveMenu('catalog/tag/all')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_tag'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('tag_id');
        $model = Mage::getModel('tag/tag');

        if ($id) {
            $model->load($id);
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('tag_tag', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('adminhtml')->__('Edit Tag') : Mage::helper('adminhtml')->__('New Tag'), $id ? Mage::helper('adminhtml')->__('Edit Tag') : Mage::helper('adminhtml')->__('New Tag'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_tag_edit')->setData('action', $this->getUrl('*/tag_edit/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $data['name']=trim($data['name']);
            $model = Mage::getModel('tag/tag');
            $model->setData($data);

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

            // $tag->setStoreId(Mage::app()->getStore()->getId());
            try {
                $model->save();
                $model->aggregate();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tag was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);
                $this->getResponse()->setRedirect($url);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);
                $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
                return;
            }
        }
        $this->getResponse()->setRedirect($url);
    }

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
                $model = Mage::getModel('tag/tag');
                $model->setId($id);
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
     * Tagged products
     *
     */
    public function productAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products'), Mage::helper('adminhtml')->__('Products'))
            ->_setActiveMenu('catalog/tag/product')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_product'))
            ->renderLayout();
    }

    /**
     * Customers
     *
     */
    public function customerAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Customers'), Mage::helper('adminhtml')->__('Customers'))
            ->_setActiveMenu('catalog/tag/customer')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_customer'))
            ->renderLayout();
    }

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
