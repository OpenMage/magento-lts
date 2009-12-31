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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * osCommerce admin controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initailization action of importController
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('oscommerce/adminhtml_import');
        return $this;
    }

    /**
     * Initialization of importController
     *
     * @param idFieldnName string
     * @return Mage_Oscommerce_Adminhtml_ImportController
     */
    protected function _initImport($idFieldName = 'id')
    {
        $id = (int) $this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('oscommerce/oscommerce');
        if ($id) {
            $model->load($id);
        }

        Mage::register('oscommerce_adminhtml_import', $model);
        return $this;
    }

    /**
     * Index action of importController
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent(
            $this->getLayout()->createBlock('oscommerce/adminhtml_import')
        );
        $this->renderLayout();
    }

    /**
     * Edit action of importController
     */
    public function editAction()
    {
        $this->_initImport();
        $this->loadLayout();

        $model = Mage::registry('oscommerce_adminhtml_import');
        $data = Mage::getSingleton('adminhtml/session')->getSystemConvertOscData(true);

        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();
        $this->_addBreadcrumb
                (Mage::helper('oscommerce')->__('Edit osCommerce Profile'),
                 Mage::helper('oscommerce')->__('Edit osCommerce Profile'));
        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('oscommerce/adminhtml_import_edit_tabs'));

        $this->_addContent($this->getLayout()->createBlock('oscommerce/adminhtml_import_edit'));

        $this->renderLayout();
    }

    /**
     * Create new action of importController
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save action of
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['send_subscription'])) {
                $data['send_subscription'] = 1;
            } else {
                $data['send_subscription'] = 0;
            }

            $this->_initImport('import_id');
            $model = Mage::registry('oscommerce_adminhtml_import');

            // Prepare saving data
            if (isset($data)) {
                $model->addData($data);
            }

//            if (empty($data['port']))
//                $data['port'] = Mage_Oscommerce_Model_Oscommerce::DEFAULT_PORT;

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oscommerce')->__('osCommerce Profile was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSystemConvertOscData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id'=>$model->getId())));
                return;
            }
        }
        if ($this->getRequest()->getParam('continue')) {
            $this->_redirect('*/*/edit', array('id'=>$model->getId()));
        } else {
            $this->_redirect('*/*');
        }
    }

    public function batchRunAction()
    {
        @set_time_limit(0);
        $this->_initImport('import_id');
        $importModel = Mage::registry('oscommerce_adminhtml_import');

        if ($tablePrefix = $importModel->getTablePrefix()) {
            $importModel->getResource()->setTablePrefix($tablePrefix);
        }

        // Start setting data from sessions
        if ($connCharset = $importModel->getSession()->getConnectionCharset()) {
            $importModel->getResource()->setConnectionCharset($connCharset);
        }
        if ($dataCharset = $importModel->getSession()->getDataCharset()) {
            $importModel->getResource()->setDataCharset($dataCharset);
        }
        if ($timezone = $importModel->getSession()->getTimezone()) {
            $importModel->setTimezone($timezone);
        }
        if ($storeLocales = $importModel->getSession()->getStoreLocales()) {
            $importModel->getResource()->setStoreLocales($storeLocales);
        }
        if ($isPoductWithCategories = $importModel->getSession()->getIsProductWithCategories()) {
            $importModel->getResource()->setIsProductWithCategories($isPoductWithCategories);
        }
        // End setting data from sessions

        // Resetting connection charset
        $importModel->getResource()->resetConnectionCharset();

           $importModel->getResource()->setImportModel($importModel);
        if ($collections =  $importModel->getResource()->importCollection($importModel->getId())) {
            if (isset($collections['website'])) {
                $importModel->getResource()->getWebsiteModel()->load($collections['website']);
            }
            if (isset($collections['root_category'])) {
                $importModel->getResource()->setRootCategory(clone $importModel->getResource()->getCategoryModel()->load($collections['root_category']));
            }
            if (isset($collections['group'])) {
                $importModel->getResource()->getStoreGroupModel()->load($collections['group']);

            }
        }

        //$isUnderDefaultWebsite = $this->getRequest()->getParam('under_default_website') ? true: false;
        $importType = $this->getRequest()->getParam('import_type');
        $importFrom = $this->getRequest()->getParam('from');
        $isImportDone = $this->getRequest()->getParam('is_done');
        switch($importType) {
            case 'products':
                    $importModel->getResource()->importProducts($importFrom, true);
                break;
            case 'categories':
                    $importModel->getResource()->importCategories($importFrom, true);
                    if ($isImportDone == 'true') {
                        $importModel->getResource()->buildCategoryPath();
                    }
                break;
            case 'customers':
                    $importModel->getResource()->importCustomers($importFrom, true, $importModel->getData('send_subscription'));
                break;
            case 'orders':
                    $importModel->getResource()->importOrders($importFrom, true);
                break;
        }

        $errors = $importModel->getResource()->getErrors();
        $result = array(
            'savedRows' => $importModel->getResource()->getSaveRows(),
            'errors'    => ( $errors ? $errors: array())
        );
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function runAction()
    {
        @set_time_limit(0);
        Mage::app()->cleanCache(); // Clean all cach
        $this->_initImport();
        $importModel = Mage::registry('oscommerce_adminhtml_import');
        /** @var $importModel Mage_Oscommerce_Model_Oscommerce */
        $totalRecords = array();

        // Start handling charsets
        $connCharset = $this->getRequest()->getParam('connection_charset');
        if ($connCharset) {
            $importModel->getSession()->setConnectionCharset($connCharset);
            $importModel->getResource()->setConnectionCharset($connCharset);
        }
        $dataCharset = $this->getRequest()->getParam('data_charset');
        if ($dataCharset) {
            $importModel->getSession()->setDataCharset($dataCharset);
            $importModel->getResource()->setDataCharset($dataCharset);
        } // End hanlding charsets

        $timezone = $this->getRequest()->getParam('timezone');
        $importModel->getSession()->setTimezone($timezone);
        $importModel->getResource()->resetConnectionCharset();

        if ($tablPrefix = $importModel->getTablePrefix()) {
            $importModel->getResource()->setTablePrefix($tablPrefix);
        }

        $importModel->getResource()->setImportModel($importModel);
        $importModel->getResource()->importCollection($importModel->getId());

        // Setting Locale for stores
        $locales = explode("|",$this->getRequest()->getParam('store_locale'));
        $storeLocales = array();
        if ($locales) foreach($locales as $locale) {
            $localeCode = explode(':', $locale);
            $storeLocales[$localeCode[0]] = $localeCode[1];
        }

        $importModel->getSession()->setStoreLocales($storeLocales);
        $importModel->getResource()->setStoreLocales($storeLocales);
        // End setting Locale for stores

        $websiteId = $this->getRequest()->getParam('website_id');
        $websiteCode = $this->getRequest()->getParam('website_code');
        $options = $this->getRequest()->getParam('import');

        // Checking Website, StoreGroup and RootCategory
        if (!$websiteId) {
            $importModel->getResource()->setWebsiteCode($websiteCode);
            $importModel->getResource()->createWebsite();
        } else {
            $importModel->getResource()->createWebsite($websiteId);
        }
        // End checking Website, StoreGroup and RootCategory

        $importModel->getResource()->importStores();
        $importModel->getResource()->importTaxClasses();
        $importModel->getResource()->createOrderTables();

        if (isset($options['categories'])) {
            $importModel->getSession()->setIsProductWithCategories(true);
            $totalRecords['categories'] = $importModel->getResource()->getCategoriesCount();
        }
        if (isset($options['products'])) {
            $totalRecords['products'] = $importModel->getResource()->getProductsCount();
        }
        if (isset($options['customers'])) {
            $totalRecords['customers'] = $importModel->getResource()->getCustomersCount();
        }
        if (isset($options['customers']) && isset($options['orders'])) {
            $totalRecords['orders'] = $importModel->getResource()->getOrdersCount();
        }
        if ($totalRecords) {
            $importModel->setTotalRecords($totalRecords);
            Mage::unRegister('oscommerce_adminhtml_import');
            Mage::register('oscommerce_adminhtml_import', $importModel);
        }
        $this->getResponse()->setBody($this->getLayout()->createBlock('oscommerce/adminhtml_import_run')->toHtml());
        $this->getResponse()->sendResponse();
    }

    public function batchFinishAction()
    {
        if ($importId = $this->getRequest()->getParam('id')) {
            $importModel = Mage::getModel('oscommerce/oscommerce')->load($importId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($importId = $importModel->getId()) {
                $importModel->deleteImportedRecords($importId);
//                $importModel->getSession()->unsStoreLocales();
//                $importModel->getSession()->unsIsProductWithCategories();
//                if ($importModel->getSession()->getTablePrefix()) {
//                    $importModel->getSession()->unsTablePrefix();
//                }
                $importModel->getSession()->clear();
            }
        }
    }

    /**
     * Delete osc action
     */
    public function deleteAction()
    {
        $this->_initImport();
        $model = Mage::registry('oscommerce_adminhtml_import');
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oscommerce')->__('osCommerce profile was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Ajax checking store
     *
     */
    public function checkStoreAction()
    {
        $this->_initImport();
        $importModel = Mage::registry('oscommerce_adminhtml_import');
        $error = false;
        if ($importModel->getId()) {
            try {
                $charset = $importModel->getResource()->getConnectionCharset();
                $defaultOscCharset = Mage_Oscommerce_Model_Mysql4_Oscommerce::DEFAULT_OSC_CHARSET;
                $defaultMageCharset = Mage_Oscommerce_Model_Mysql4_Oscommerce::DEFAULT_MAGENTO_CHARSET;

                $stores = $importModel->getResource()->getOscStores();

                $locales = Mage::app()->getLocale()->getOptionLocales();
                $options = '';
                foreach ($locales as $locale) {
                    $options .= "<option value='".$locale['value']."' ".($locale['value']=='en_US'?'selected':'').">{$locale['label']}</option>";
                }
                $html = '';
                if ($stores) {
                    $html .= "<table>\n";
                    foreach ($stores as $store) {
                        $html .= "<tr><td style='width: 100px'>" . $importModel->getResource()->convert($store['name']) . " Store</td><td>";
                        $html .= "<select id='store_locale_{$store['code']}' name='store[{$store['code']}'";
                        $html .= ">{$options}</select>";
                        $html .= "</td></tr>\n";
                    }
                    $html .= "</table>\n";
                }
            } catch (Exception $e) {
                $error = true;
                $html = (preg_match("/Column not found/",$e->getMessage())? Mage::helper('oscommerce')->__('languages table error '):'') . $e->getMessage();
            }

            if ($error) {
                $result = array(
                    'error'    => true,
                    'messages' => $html
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            } else {
                $this->getResponse()->setBody($html);
            }
        }
    }

    public function checkWebsiteCodeAction()
    {

        $this->_initImport();
        $model = Mage::registry('oscommerce_adminhtml_import');
        if ($model->getId()) {
            $website = Mage::getModel('core/website');
            $collections = $website->getCollection();
            $result = 'false';
            $websiteCode = $this->getRequest()->getParam('website_code');
            if ($collections) foreach ($collections as $collection) {
                if ($collection->getCode() == $websiteCode) {
                    $result = 'true';
                }
            }
            $this->getResponse()->setBody($result);
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/oscimport');
    }
}
