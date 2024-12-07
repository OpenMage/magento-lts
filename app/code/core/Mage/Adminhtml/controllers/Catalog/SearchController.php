<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Catalog_SearchController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/search';

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/search')
            ->_addBreadcrumb(Mage::helper('catalog')->__('Search'), Mage::helper('catalog')->__('Search'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Search Terms'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/catalog_search'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Search Terms'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalogsearch/query');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This search no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('current_catalog_search', $model);

        $this->_initAction();

        $this->_title($id ? $model->getQueryText() : $this->__('New Search'));

        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

        $this->getLayout()->getBlock('catalog_search_edit')
            ->setData('action', $this->getUrl('*/catalog_search/save'));

        $this
            ->_addBreadcrumb($id ? Mage::helper('catalog')->__('Edit Search') : Mage::helper('catalog')->__('New Search'), $id ? Mage::helper('catalog')->__('Edit Search') : Mage::helper('catalog')->__('New Search'));

        $this->renderLayout();
    }

    /**
     * Save search query
     *
     */
    public function saveAction()
    {
        $hasError   = false;
        $data       = $this->getRequest()->getPost();
        $queryId    = $this->getRequest()->getPost('query_id', null);
        if ($this->getRequest()->isPost() && $data) {
            /** @var Mage_CatalogSearch_Model_Query $model */
            $model = Mage::getModel('catalogsearch/query');

            // validate query
            $queryText  = $this->getRequest()->getPost('query_text', false);
            $storeId    = $this->getRequest()->getPost('store_id', false);

            try {
                if ($queryText) {
                    $model->setStoreId($storeId);
                    $model->loadByQueryText($queryText);
                    if ($model->getId() && $model->getId() != $queryId) {
                        Mage::throwException(
                            Mage::helper('catalog')->__('Search Term with such search query already exists.')
                        );
                    } elseif (!$model->getId() && $queryId) {
                        $model->load($queryId);
                    }
                } elseif ($queryId) {
                    $model->load($queryId);
                }

                $model->addData($data);
                $model->setIsProcessed(0);
                $model->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('catalog')->__('You saved the search term.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $hasError = true;
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('catalog')->__('An error occurred while saving the search query.')
                );
                $hasError = true;
            }
        }

        if ($hasError) {
            $this->_getSession()->setPageData($data);
            $this->_redirect('*/*/edit', ['id' => $queryId]);
        } else {
            $this->_redirect('*/*');
        }
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('catalogsearch/query');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('The search was deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Unable to find a search term to delete.'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $searchIds = $this->getRequest()->getParam('search');
        if (!is_array($searchIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select catalog searches.'));
        } else {
            try {
                foreach ($searchIds as $searchId) {
                    $model = Mage::getModel('catalogsearch/query')->load($searchId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', count($searchIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete', 'massDelete');
        return parent::preDispatch();
    }
}
