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
 * Adminhtml tax rate controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Tax_RateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Show Main Grid
     *
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('tax')->__('Manage Tax Rates'), Mage::helper('tax')->__('Manage Tax Rates'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_add', 'tax_rate_toolbar')
                    ->assign('createUrl', $this->getUrl('*/tax_rate/add'))
                    ->assign('header', Mage::helper('tax')->__('Manage Tax Rates'))
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rate_grid', 'tax_rate_grid'))
            ->renderLayout();
    }

    /**
     * Show Add Form
     *
     */
    public function addAction()
    {
        $rateModel = Mage::getSingleton('tax/calculation_rate')
            ->load(null);
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('tax')->__('Manage Tax Rates'), Mage::helper('tax')->__('Manage Tax Rates'), $this->getUrl('*/tax_rate'))
            ->_addBreadcrumb(Mage::helper('tax')->__('New Tax Rate'), Mage::helper('tax')->__('New Tax Rate'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_save')
                ->assign('header', Mage::helper('tax')->__('Add New Tax Rate'))
                ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_rate_form'))
            )
            ->renderLayout();
    }

    /**
     * Save Rate and Data
     *
     * @return bool
     */
    public function saveAction()
    {
        if ($ratePost = $this->getRequest()->getPost()) {
            $ratePostData = $this->getRequest()->getPost('rate_data');
            $rateModel = Mage::getModel('tax/calculation_rate')->setData($ratePost);

            try {
                $rateModel->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Tax rate was successfully saved'));
                $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                return true;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                //Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Error while saving this rate. Please try again later.'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            $this->_redirectReferer();
        }
    }

    /**
     * Show Edit Form
     *
     */
    public function editAction()
    {
        $rateId = (int)$this->getRequest()->getParam('rate');
        $rateModel = Mage::getSingleton('tax/calculation_rate')
            ->load($rateId);
        if (!$rateModel->getId()) {
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return ;
        }

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('tax')->__('Manage Tax Rates'), Mage::helper('tax')->__('Manage Tax Rates'), $this->getUrl('*/tax_rate'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Edit Tax Rate'), Mage::helper('tax')->__('Edit Tax Rate'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_save')
                ->assign('header', Mage::helper('tax')->__('Edit Tax Rate'))
                ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_rate_form'))
            )
            ->renderLayout();
    }

    /**
     * Delete Rate and Data
     *
     * @return bool
     */
    public function deleteAction()
    {
        if ($rateId = $this->getRequest()->getParam('rate')) {
            $rateModel = Mage::getModel('tax/calculation_rate')->load($rateId);
            if ($rateModel->getId()) {
                try {
                    $rateModel->delete();

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Tax rate was successfully deleted'));
                    $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                    return true;
                }
                catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Error while deleting this rate. Please try again later.'));
                }
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                else {
                    $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Error while deleting this rate. Incorrect rate ID'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            }
        }
    }

    /**
     * Export rates grid to CSV format
     *
     */
    public function exportCsvAction()
    {
        $fileName   = 'rates.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/tax_rate_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export rates grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'rates.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/tax_rate_grid')
            ->getXml();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tax_rates')
            ->_addBreadcrumb(Mage::helper('tax')->__('Sales'), Mage::helper('tax')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax'), Mage::helper('tax')->__('Tax'));
        return $this;
    }

    /**
     * Import and export Page
     *
     */
    public function importExportAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tax_importExport')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rate_importExport'))
            ->renderLayout();
    }

    /**
     * import action from import/export tax
     *
     */
    public function importPostAction()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_rates_file']['tmp_name'])) {
            try {
                $this->_importRates();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Tax rate was successfully imported'));
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Invalid file upload attempt'));
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/importExport');
    }

    protected function _importRates()
    {
        $fileName   = $_FILES['import_rates_file']['tmp_name'];
        $csvObject  = new Varien_File_Csv();
        $csvData = $csvObject->getData($fileName);

        /** checks columns */
        $csvFields  = array(
            0   => Mage::helper('tax')->__('Code'),
            1   => Mage::helper('tax')->__('Country'),
            2   => Mage::helper('tax')->__('State'),
            3   => Mage::helper('tax')->__('Zip/Post Code'),
            4   => Mage::helper('tax')->__('Rate')
        );


        $stores = array();
        $unset = array();
        $storeCollection = Mage::getModel('core/store')->getCollection()->setLoadDefault(false);
        for ($i=5; $i<count($csvData[0]); $i++) {
            $header = $csvData[0][$i];
            $found = false;
            foreach ($storeCollection as $store) {
                if ($header == $store->getCode()) {
                    $csvFields[$i] = $store->getCode();
                    $stores[$i] = $store->getId();
                    $found = true;
                }
            }
            if (!$found) {
                $unset[] = $i;
            }

        }

        $regions = array();

        if ($unset) {
            foreach ($unset as $u) {
                unset($csvData[0][$u]);
            }
        }
        if ($csvData[0] == $csvFields) {
            foreach ($csvData as $k => $v) {
                if ($k == 0) {
                    continue;
                }

                //end of file has more then one empty lines
                if (count($v) <= 1 && !strlen($v[0])) {
                    continue;
                }
                if ($unset) {
                    foreach ($unset as $u) {
                        unset($v[$u]);
                    }
                }

                if (count($csvFields) != count($v)) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Invalid file upload attempt'));
                }

                $country = Mage::getModel('directory/country')->loadByCode($v[1], 'iso2_code');
                if (!$country->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('One of the country has invalid code.'));
                    continue;
                }

                if (!isset($regions[$v[1]])) {
                    $regions[$v[1]]['*'] = '*';
                    $regionCollection = Mage::getModel('directory/region')->getCollection()
                        ->addCountryFilter($v[1]);
                    if ($regionCollection->getSize()) {
                        foreach ($regionCollection as $region) {
                            $regions[$v[1]][$region->getCode()] = $region->getRegionId();
                        }
                    }
                }

                if (!empty($regions[$v[1]][$v[2]])) {
                    $rateData  = array(
                        'code'=>$v[0],
                        'tax_country_id' => $v[1],
                        'tax_region_id' => ($regions[$v[1]][$v[2]] == '*') ? 0 : $regions[$v[1]][$v[2]],
                        'tax_postcode'  => (empty($v[3]) || $v[3]=='*') ? null : $v[3],
                        'rate'=>$v[4],
                    );

                    $rateModel = Mage::getModel('tax/calculation_rate')->loadByCode($rateData['code']);
                    foreach($rateData as $dataName => $dataValue) {
                        $rateModel->setData($dataName, $dataValue);
                    }

                    $titles = array();
                    foreach ($stores as $field=>$id) {
                        $titles[$id]=$v[$field];
                    }
                    $rateModel->setTitle($titles);
                    $rateModel->save();
                }
            }
        }
        else {
            Mage::throwException(Mage::helper('tax')->__('Invalid file format upload attempt'));
        }
    }

    /**
     * export action from import/export tax
     *
     */
    public function exportPostAction()
    {
        /** get rate types */
        $stores = array();
        $storeCollection = Mage::getModel('core/store')->getCollection()->setLoadDefault(false);
        foreach ($storeCollection as $store) {
            $stores[$store->getId()] = $store->getCode();
        }

        /** start csv content and set template */
        $content    = '"'.Mage::helper('tax')->__('Code').'","'.Mage::helper('tax')->__('Country').'","'.Mage::helper('tax')->__('State').'","'.Mage::helper('tax')->__('Zip/Post Code').'","'.Mage::helper('tax')->__('Rate').'"';
        $template   = '"{{code}}","{{country_name}}","{{region_name}}","{{tax_postcode}}","{{rate}}"';
        foreach ($stores as $id => $name) {
            $content   .= ',"'.$name.'"';
            $template  .= ',"{{title_'.$id.'}}"';
        }
        $content .= "\n";

        $rateCollection = Mage::getModel('tax/calculation_rate')->getCollection()
            ->joinStoreTitles()
            ->joinCountryTable()
            ->joinRegionTable();
        foreach ($rateCollection as $rate) {
            if ($rate->getTaxRegionId() == 0) {
                $rate->setRegionName('*');
            }
            $content .= $rate->toString($template)."\n";
        }

        $fileName = 'tax_rates.csv';

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {

        switch ($this->getRequest()->getActionName()) {
            case 'importExport':
                return Mage::getSingleton('admin/session')->isAllowed('sales/tax/import_export');
                break;
            case 'index':
                return Mage::getSingleton('admin/session')->isAllowed('sales/tax/rates');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('sales/tax/rates');
                break;
        }
    }
}
