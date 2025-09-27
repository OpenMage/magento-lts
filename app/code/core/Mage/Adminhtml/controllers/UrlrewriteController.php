<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Urlrewrites adminhtml controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_UrlrewriteController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/urlrewrite';

    /**
     * Instantiate urlrewrite, product and category
     *
     * @return $this
     */
    protected function _initRegistry()
    {
        $this->_title($this->__('Rewrite Rules'));

        // initialize urlrewrite, product and category models
        Mage::register(
            'current_urlrewrite',
            Mage::getSingleton('core/factory')->getUrlRewriteInstance()
            ->load($this->getRequest()->getParam('id', 0)),
        );
        $productId  = $this->getRequest()->getParam('product', 0);
        $categoryId = $this->getRequest()->getParam('category', 0);
        if (Mage::registry('current_urlrewrite')->getId()) {
            $productId  = Mage::registry('current_urlrewrite')->getProductId();
            $categoryId = Mage::registry('current_urlrewrite')->getCategoryId();
        }

        Mage::register('current_product', Mage::getModel('catalog/product')->load($productId));
        Mage::register('current_category', Mage::getModel('catalog/category')->load($categoryId));

        return $this;
    }

    /**
     * Show urlrewrites index page
     *
     */
    public function indexAction()
    {
        $this->_initRegistry();
        $this->loadLayout();
        $this->_setActiveMenu('catalog/urlrewrite');
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/urlrewrite'),
        );
        $this->renderLayout();
    }

    /**
     * Show urlrewrite edit/create page
     *
     */
    public function editAction()
    {
        $this->_initRegistry();

        $this->_title($this->__('URL Rewrite'));

        $this->loadLayout();
        $this->_setActiveMenu('catalog/urlrewrite');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/urlrewrite_edit'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * Ajax products grid action
     *
     */
    public function productGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/urlrewrite_product_grid')->toHtml());
    }

    /**
     * Ajax categories tree loader action
     *
     */
    public function categoriesJsonAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(
            Mage::getBlockSingleton('adminhtml/urlrewrite_category_tree')
            ->getTreeArray($id, true, 1),
        );
    }

    /**
     * Urlrewrite save action
     *
     */
    public function saveAction()
    {
        $this->_initRegistry();

        if ($data = $this->getRequest()->getPost()) {
            $session = Mage::getSingleton('adminhtml/session');
            try {
                // set basic urlrewrite data
                $model = Mage::registry('current_urlrewrite');

                // Validate request path
                $requestPath = $this->getRequest()->getParam('request_path');
                Mage::helper('core/url_rewrite')->validateRequestPath($requestPath);

                // Proceed and save request
                $model->setIdPath($this->getRequest()->getParam('id_path'))
                    ->setTargetPath($this->getRequest()->getParam('target_path'))
                    ->setOptions($this->getRequest()->getParam('options'))
                    ->setDescription($this->getRequest()->getParam('description'))
                    ->setRequestPath(strtolower($requestPath));

                if (!$model->getId()) {
                    $model->setIsSystem(0);
                }
                if (!$model->getIsSystem()) {
                    $model->setStoreId($this->getRequest()->getParam('store_id', 0));
                }

                // override urlrewrite data, basing on current registry combination
                $category = Mage::registry('current_category')->getId() ? Mage::registry('current_category') : null;
                if ($category) {
                    $model->setCategoryId($category->getId());
                }
                $product  = Mage::registry('current_product')->getId() ? Mage::registry('current_product') : null;
                if ($product) {
                    $model->setProductId($product->getId());
                }
                if ($product || $category) {
                    $catalogUrlModel = Mage::getSingleton('catalog/url');
                    $idPath = $catalogUrlModel->generatePath('id', $product, $category);

                    // if redirect specified try to find friendly URL
                    $found = false;
                    if (in_array($model->getOptions(), ['R', 'RP'])) {
                        $rewrite = Mage::getResourceModel('catalog/url')
                            ->getRewriteByIdPath($idPath, $model->getStoreId());
                        if (!$rewrite) {
                            $exceptionTxt = 'Chosen product does not associated with the chosen store or category.';
                            Mage::throwException($exceptionTxt);
                        }
                        if ($rewrite->getId() && $rewrite->getId() != $model->getId()) {
                            $model->setIdPath($idPath);
                            $model->setTargetPath($rewrite->getRequestPath());
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $model->setIdPath($idPath);
                        $model->setTargetPath($catalogUrlModel->generatePath('target', $product, $category));
                    }
                }

                // save and redirect
                $model->save();
                $session->addSuccess(Mage::helper('adminhtml')->__('The URL Rewrite has been saved.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage())
                    ->setUrlrewriteData($data);
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('adminhtml')->__('An error occurred while saving URL Rewrite.'))
                    ->setUrlrewriteData($data);
                // return intentionally omitted
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Urlrewrite delete action
     *
     */
    public function deleteAction()
    {
        $this->_initRegistry();

        if (Mage::registry('current_urlrewrite')->getId()) {
            try {
                Mage::registry('current_urlrewrite')->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The URL Rewrite has been deleted.'),
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addException($e, Mage::helper('adminhtml')->__('An error occurred while deleting URL Rewrite.'));
                $this->_redirect('*/*/edit/', ['id' => Mage::registry('current_urlrewrite')->getId()]);
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}
