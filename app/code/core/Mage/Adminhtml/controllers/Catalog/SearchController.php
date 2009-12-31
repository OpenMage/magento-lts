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


class Mage_Adminhtml_Catalog_SearchController extends Mage_Adminhtml_Controller_Action
{
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
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalogsearch/query');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This search no longer exists'));
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

        $block = $this->getLayout()->createBlock('adminhtml/catalog_search_edit')
            ->setData('action', $this->getUrl('*/catalog_search/save'));

        $this->_initAction();

        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

        $this
            ->_addBreadcrumb($id ? Mage::helper('catalog')->__('Edit Search') : Mage::helper('catalog')->__('New Search'), $id ? Mage::helper('catalog')->__('Edit Search') : Mage::helper('catalog')->__('New Search'))
            ->_addContent($block)
            ->renderLayout();

    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('catalogsearch/query');

            if ($queryText = $this->getRequest()->getParam('query_text')) {
                $model->load($queryText);
                if ($model->getId() && $this->getRequest()->getParam('query_id') != $model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Search Term with such search query already exist.'));
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('query_id')));
                    return;
                }
            }

            $model->addData($data);
            Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('Search Term was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('catalogsearch/query');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('Search was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Unable to find a search term to delete'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $searchIds = $this->getRequest()->getParam('search');
        if(!is_array($searchIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select catalog searches'));
        } else {
            try {
                foreach ($searchIds as $searchId) {
                    $model = Mage::getModel('catalogsearch/query')->load($searchId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($searchIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/search');
    }
}
