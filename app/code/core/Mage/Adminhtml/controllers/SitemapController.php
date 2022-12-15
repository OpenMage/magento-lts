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
 * Google sitemap controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_SitemapController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/sitemap';

    /**
     * Maximum sitemap name length
     */
    public const MAXIMUM_SITEMAP_NAME_LENGTH = 32;

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        return parent::preDispatch();
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog/system_sitemap')
            ->_addBreadcrumb(
                Mage::helper('catalog')->__('Catalog'),
                Mage::helper('catalog')->__('Catalog')
            )
            ->_addBreadcrumb(
                Mage::helper('sitemap')->__('Google Sitemap'),
                Mage::helper('sitemap')->__('Google Sitemap')
            )
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Google Sitemaps'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/sitemap'))
            ->renderLayout();
    }

    /**
     * Create new sitemap
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit sitemap
     */
    public function editAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Google Sitemaps'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('sitemap_id');
        $model = Mage::getModel('sitemap/sitemap');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('sitemap')->__('This sitemap no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getSitemapFilename() : $this->__('New Sitemap'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('sitemap_sitemap', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('sitemap')->__('Edit Sitemap') : Mage::helper('sitemap')->__('New Sitemap'),
                $id ? Mage::helper('sitemap')->__('Edit Sitemap') : Mage::helper('sitemap')->__('New Sitemap')
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/sitemap_edit'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $model = Mage::getModel('sitemap/sitemap');

            //validate path to generate
            if (!empty($data['sitemap_filename']) && !empty($data['sitemap_path'])) {
                $path = rtrim($data['sitemap_path'], '\\/')
                      . DS . $data['sitemap_filename'];

                // check filename length
                if (strlen($data['sitemap_filename']) > self::MAXIMUM_SITEMAP_NAME_LENGTH) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('sitemap')->__(
                            'Please enter a sitemap name with at most %s characters.',
                            self::MAXIMUM_SITEMAP_NAME_LENGTH
                        )
                    );
                    $this->_redirect('*/*/edit', [
                        'sitemap_id' => $this->getRequest()->getParam('sitemap_id')
                    ]);
                    return;
                }
                /** @var Mage_Core_Model_File_Validator_AvailablePath $validator */
                $validator = Mage::getModel('core/file_validator_availablePath');
                /** @var Mage_Adminhtml_Helper_Catalog $helper */
                $helper = Mage::helper('adminhtml/catalog');
                $validator->setPaths($helper->getSitemapValidPaths());
                if (!$validator->isValid($path)) {
                    foreach ($validator->getMessages() as $message) {
                        Mage::getSingleton('adminhtml/session')->addError($message);
                    }
                    // save data in session
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    // redirect to edit form
                    $this->_redirect('*/*/edit', [
                        'sitemap_id' => $this->getRequest()->getParam('sitemap_id')]);
                    return;
                }
            }

            if ($this->getRequest()->getParam('sitemap_id')) {
                $model ->load($this->getRequest()->getParam('sitemap_id'));

                if ($model->getSitemapFilename() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
            }

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('sitemap')->__('The sitemap has been saved.')
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['sitemap_id' => $model->getId()]);
                    return;
                }
                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('sitemap_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', [
                    'sitemap_id' => $this->getRequest()->getParam('sitemap_id')]);
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('sitemap_id')) {
            try {
                // init model and delete
                /** @var Mage_Sitemap_Model_Sitemap $model */
                $model = Mage::getModel('sitemap/sitemap');
                $model->setId($id);
                // init and load sitemap model

                $model->load($id);
                // delete file
                if ($model->getSitemapFilename() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('sitemap')->__('The sitemap has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', ['sitemap_id' => $id]);
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('sitemap')->__('Unable to find a sitemap to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Generate sitemap
     */
    public function generateAction()
    {
        // init and load sitemap model
        $id = $this->getRequest()->getParam('sitemap_id');
        $sitemap = Mage::getModel('sitemap/sitemap');
        /** @var Mage_Sitemap_Model_Sitemap $sitemap */
        $sitemap->load($id);
        // if sitemap record exists
        if ($sitemap->getId()) {
            try {
                $sitemap->generateXml();

                $this->_getSession()->addSuccess(
                    Mage::helper('sitemap')->__('The sitemap "%s" has been generated.', $sitemap->getSitemapFilename())
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sitemap')->__('Unable to generate the sitemap.')
                );
            }
        } else {
            $this->_getSession()->addError(
                Mage::helper('sitemap')->__('Unable to find a sitemap to generate.')
            );
        }

        // go to grid
        $this->_redirect('*/*/');
    }
}
