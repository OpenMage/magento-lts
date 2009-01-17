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
 * @package    Mage_Install
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
@set_time_limit(120);
/**
 * Installation wizard controller
 */
class Mage_Install_WizardController extends Mage_Install_Controller_Action
{
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        return parent::preDispatch();
    }

    /**
     * Retrieve installer object
     *
     * @return Mage_Install_Model_Installer
     */
    protected function _getInstaller()
    {
        return Mage::getSingleton('install/installer');
    }

    /**
     * Retrieve wizard
     *
     * @return Mage_Install_Model_Wizard
     */
    protected function _getWizard()
    {
        return Mage::getSingleton('install/wizard');
    }

    /**
     * Prepare layout
     *
     * @return unknown
     */
    protected function _prepareLayout()
    {
        $this->loadLayout('install_wizard');
        $step = $this->_getWizard()->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }

        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        return $this;
    }

    /**
     * Checking installation status
     *
     * @return unknown
     */
    protected function _checkIfInstalled()
    {
        if ($this->_getInstaller()->isApplicationInstalled()) {
            $this->getResponse()->setRedirect(Mage::getBaseUrl())->sendResponse();
            exit;
        }
        return true;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_forward('begin');
    }

    /**
     * Begin installation action
     */
    public function beginAction()
    {
        $this->_checkIfInstalled();

        $this->setFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/begin', 'install.begin')
        );

        $this->renderLayout();
    }

    /**
     * Process begin step POST data
     */
    public function beginPostAction()
    {
        $this->_checkIfInstalled();

        $agree = $this->getRequest()->getPost('agree');
        if ($agree && $step = $this->_getWizard()->getStepByName('begin')) {
            $this->getResponse()->setRedirect($step->getNextUrl());
        }
        else {
            $this->_redirect('install');
        }
    }

    /**
     * Localization settings
     */
    public function localeAction()
    {
        $this->_checkIfInstalled();
        $this->setFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/locale', 'install.locale')
        );

        $this->renderLayout();
    }

    /**
     * Change current locale
     */
    public function localeChangeAction()
    {
        $this->_checkIfInstalled();

        $locale = $this->getRequest()->getParam('locale');
        if ($locale) {
            Mage::getSingleton('install/session')->setLocale($locale);
        }

        $this->_redirect('*/*/locale');
    }

    /**
     * Saving localization settings
     */
    public function localePostAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('locale');

        if ($data = $this->getRequest()->getPost('config')) {
            Mage::getSingleton('install/session')->setLocaleData($data);
        }

        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    public function downloadAction()
    {
        $this->_checkIfInstalled();
        $this->setFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/download', 'install.download')
        );

        $this->renderLayout();
    }

    public function downloadPostAction()
    {
        $this->_checkIfInstalled();
        switch ($this->getRequest()->getPost('continue')) {
            case 'auto':
                $this->_forward('downloadAuto');
                break;

            case 'manual':
                $this->_forward('downloadManual');
                break;

            case 'svn':
                $step = $this->_getWizard()->getStepByName('download');
                $this->getResponse()->setRedirect($step->getNextUrl());
                break;

            default:
                $this->_redirect('*/*/download');
        }
    }

    public function downloadAutoAction()
    {
        $step = $this->_getWizard()->getStepByName('download');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    public function installAction()
    {
        $pear = Varien_Pear::getInstance();
        $params = array('comment'=>Mage::helper('install')->__("Downloading and installing Magento, please wait...")."\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            if ($state = $this->getRequest()->getParam('state', 'beta')) {
                $result = $pear->runHtmlConsole(array(
                'comment'   => Mage::helper('install')->__("Setting preferred state to: %s", $state)."\r\n\r\n",
                'command'   => 'config-set',
                'params'    => array('preferred_state', $state)
                ));
                if ($result instanceof PEAR_Error) {
                    $this->installFailureCallback();
                    exit;
                }
            }
            $params['command'] = 'install';
            $params['options'] = array('onlyreqdeps'=>1);
            $params['params'] = Mage::getModel('install/installer_pear')->getPackages();
            $params['success_callback'] = array($this, 'installSuccessCallback');
            $params['failure_callback'] = array($this, 'installFailureCallback');
        }
        $pear->runHtmlConsole($params);
        Mage::app()->getFrontController()->getResponse()->clearAllHeaders();
    }

    public function installSuccessCallback()
    {
        echo 'parent.installSuccess()';
    }

    public function installFailureCallback()
    {
        echo 'parent.installFailure()';
    }

    public function downloadManualAction()
    {
        $step = $this->_getWizard()->getStepByName('download');
        #if (!$this->_getInstaller()->checkDownloads()) {
        #    $this->getResponse()->setRedirect($step->getUrl());
        #} else {
        $this->getResponse()->setRedirect($step->getNextUrl());
        #}
    }

    /**
     * Configuration data installation
     */
    public function configAction()
    {
        $this->_checkIfInstalled();
        $this->_getInstaller()->checkServer();

        $this->setFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/config', 'install.config')
        );

        $this->renderLayout();
    }

    /**
     * Process configuration POST data
     */
    public function configPostAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');

        if ($data = $this->getRequest()->getPost('config')) {
            //make all table prefix to lower letter
            if ($data['db_prefix'] !='') {
               $data['db_prefix'] = strtolower($data['db_prefix']);
            }

            Mage::getSingleton('install/session')
                ->setConfigData($data)
                ->setSkipUrlValidation($this->getRequest()->getPost('skip_url_validation'))
                ->setSkipBaseUrlValidation($this->getRequest()->getPost('skip_base_url_validation'));
            try {
                if($data['db_prefix']!='') {
                    if(!preg_match('/^[a-z]+[a-z0-9_]*$/',$data['db_prefix'])) {
                        Mage::throwException(
                            Mage::helper('install')->__('Table prefix should contain only letters (a-z), numbers (0-9) or underscore(_), first character should be a letter'));
                    }
                }
                $this->_getInstaller()->installConfig($data);
                $this->_redirect('*/*/installDb');
                return $this;
            }
            catch (Exception $e){
                Mage::getSingleton('install/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($step->getUrl());
            }
        }
        $this->getResponse()->setRedirect($step->getUrl());
    }

    /**
     * Install DB
     */
    public function installDbAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');
        try {
            $this->_getInstaller()->installDb();
            /**
             * Clear session config data
             */
            Mage::getSingleton('install/session')->getConfigData(true);
            $this->getResponse()->setRedirect($step->getNextUrl());
        }
        catch (Exception $e){
            Mage::getSingleton('install/session')->addError($e->getMessage());
            $this->getResponse()->setRedirect($step->getUrl());
        }
    }

    /**
     * Install admininstrator account
     */
    public function administratorAction()
    {
        $this->_checkIfInstalled();

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/admin', 'install.administrator')
        );
        $this->renderLayout();
    }

    /**
     * Process administrator instalation POST data
     */
    public function administratorPostAction()
    {
        $this->_checkIfInstalled();

        $step = Mage::getSingleton('install/wizard')->getStepByName('administrator');
        $adminData      = $this->getRequest()->getPost('admin');
        $encryptionKey  = $this->getRequest()->getPost('encryption_key');

        try {
            $this->_getInstaller()->createAdministrator($adminData)
                ->installEnryptionKey($encryptionKey);
        }
        catch (Exception $e){
            Mage::getSingleton('install/session')
                ->setAdminData($adminData)
                ->addError($e->getMessage());
            $this->getResponse()->setRedirect($step->getUrl());
            return false;
        }
        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    /**
     * End installation
     */
    public function endAction()
    {
        $this->_checkIfInstalled();

        $date = (string)Mage::getConfig()->getNode('global/install/date');
        if ($date !== Mage_Install_Model_Installer_Config::TMP_INSTALL_DATE_VALUE) {
            $this->_redirect('*/*');
            return;
        }

        $this->_getInstaller()->finish();

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/end', 'install.end')
        );
        $this->renderLayout();
        Mage::getSingleton('install/session')->clear();
    }

    /**
     * Host validation response
     */
    public function checkHostAction()
    {
        $this->getResponse()->setHeader('Transfer-encoding', '', true);
        $this->getResponse()->setBody(Mage_Install_Model_Installer::INSTALLER_HOST_RESPONSE);
    }

    /**
     * Host validation response
     */
    public function checkSecureHostAction()
    {
        $this->getResponse()->setHeader('Transfer-encoding', '', true);
        $this->getResponse()->setBody(Mage_Install_Model_Installer::INSTALLER_HOST_RESPONSE);
    }
}
