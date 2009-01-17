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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_UrlrewriteController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Create initial action
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/urlrewrite');
        return $this;
    }

    protected function _initUrlrewrite($idFieldName = 'id')
    {
        $id = (int) $this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('core/url_rewrite');

        if ($id) {
            $model->load($id);
        }

        Mage::register('urlrewrite_urlrewrite', $model);
        return $this;
    }

    /**
     * Create index url action
     */
    public function indexAction()
    {
    	$this->_initAction();
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/urlrewrite')
        );

        $this->renderLayout();
    }

    /**
     * Delete urlrewrite action
     */
    public function deleteAction()
    {
        $this->_initUrlrewrite();
        $model = Mage::registry('urlrewrite_urlrewrite');
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Urlrewrite was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/urlrewrite');
    }

    /**
     * Create edit url action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('core/url_rewrite');
        if ($model) {
        	$model->load($id);
        }

        Mage::register('urlrewrite_urlrewrite', $model);

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Url'),
            Mage::helper('adminhtml')->__('Edit Url'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/urlrewrite_edit'))
            ->renderLayout();
    }

    /**
     * Create new url action
     */
    public function newAction()
    {
        $this->loadLayout();

//        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_initAction()->_addContent($this->getLayout()->createBlock('adminhtml/urlrewrite_add'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    /**
     * Save urlrewrite action
     */
    public function saveAction()
    {

        if ($data = $this->getRequest()->getPost()) {
            $this->_initUrlrewrite();
            $model = Mage::registry('urlrewrite_urlrewrite');

            // Saving urlrewrite data
            try {
            	if (!$model->getId()) {
            		$model->setIsSystem(0);
            		$model->setStoreId($data['store_id']);
                    $model->setIdPath($data['id_path']);
                    $model->setTargetPath($data['target_path']);
                    $model->setProductId($data['product_id'] ? $data['product_id'] : null);
                    $model->setCategoryId($data['category_id'] ? $data['category_id'] : null);
            	}
            	$model->setRequestPath($data['request_path']);
            	$model->setOptions($data['options']);
                $model->setDescription($data['description']);
            	$model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Urlrewrite was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage())->setUrlrewriteData($data);

                $this->getResponse()->setRedirect($this->getUrl('*/urlrewrite/edit', array('id'=>$model->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/urlrewrite'));
    }

    public function jsonProductInfoAction()
    {
        $response = new Varien_Object();
        $id = $this->getRequest()->getParam('id');
        if( intval($id) > 0 ) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            $response->setId($id);
            $response->addData($product->getData());
            $response->setError(0);
        } else {
            $response->setError(1);
            $response->setMessage(Mage::helper('adminhtml')->__('Unable to get product id.'));
        }
        $this->getResponse()->setBody($response->toJSON());
    }

    public function productGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/urlrewrite_product_grid')->toHtml());
    }

    public function getCategoryInfoAction()
    {
        $response = new Varien_Object();
        $id = $this->getRequest()->getParam('id');
        if( intval($id) > 0 ) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            Mage::register('product', $product);
            $tree = new Mage_Adminhtml_Block_Urlrewrite_Category_Tree();

        } else {
            $tree = new Mage_Adminhtml_Block_Urlrewrite_Category_Tree();
        }

       $this->getResponse()->setBody($tree->getTreeJson());
    }

    private function _formatUrlKey($str)
    {
    	$urlKey = preg_replace('#[^0-9a-z\/\.]+#i', '-', $str);
    	$urlKey = strtolower($urlKey);
    	$urlKey = trim($urlKey, '-');

    	return $urlKey;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/urlrewrite');
    }
}