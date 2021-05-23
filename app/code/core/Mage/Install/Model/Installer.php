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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Installer model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer extends Varien_Object
{

    /**
     * Installer host response used to check urls
     *
     */
    const INSTALLER_HOST_RESPONSE   = 'MAGENTO';

    /**
     * Installer data model used to store data between installation steps
     *
     * @var Varien_Object
     */
    protected $_dataModel;

    /**
     * Checking install status of application
     *
     * @return bool
     */
    public function isApplicationInstalled()
    {
        return Mage::isInstalled();
    }

    /**
     * Get data model
     *
     * @return Varien_Object
     */
    public function getDataModel()
    {
        if (is_null($this->_dataModel)) {
            $this->setDataModel(Mage::getSingleton('install/session'));
        }
        return $this->_dataModel;
    }

    /**
     * Set data model to store data between installation steps
     *
     * @param Varien_Object $model
     * @return $this
     */
    public function setDataModel(Varien_Object $model)
    {
        $this->_dataModel = $model;
        return $this;
    }

    /**
     * Check server settings
     *
     * @return bool
     */
    public function checkServer()
    {
        try {
            Mage::getModel('install/installer_filesystem')->install();

            Mage::getModel('install/installer_env')->install();
            $result = true;
        } catch (Exception $e) {
            $result = false;
        }
        $this->setData('server_check_status', $result);
        return $result;
    }

    /**
     * Retrieve server checking result status
     *
     * @return unknown
     */
    public function getServerCheckStatus()
    {
        $status = $this->getData('server_check_status');
        if (is_null($status)) {
            $status = $this->checkServer();
        }
        return $status;
    }

    /**
     * Installation config data
     *
     * @param   array $data
     * @return  Mage_Install_Model_Installer
     */
    public function installConfig($data)
    {
        $data['db_active'] = true;

        $data = Mage::getSingleton('install/installer_db')->checkDbConnectionData($data);

        Mage::getSingleton('install/installer_config')
            ->setConfigData($data)
            ->install();
        return $this;
    }

    /**
     * Database installation
     *
     * @return $this
     */
    public function installDb()
    {
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        $data = $this->getDataModel()->getConfigData();

        /**
         * Saving host information into DB
         */
        $setupModel = new Mage_Core_Model_Resource_Setup('core_setup');

        if (!empty($data['use_rewrites'])) {
            $setupModel->setConfigData(Mage_Core_Model_Store::XML_PATH_USE_REWRITES, 1);
        }

        if (!empty($data['enable_charts'])) {
            $setupModel->setConfigData(Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS, 1);
        } else {
            $setupModel->setConfigData(Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS, 0);
        }


        $unsecureBaseUrl = Mage::getBaseUrl('web');
        if (!empty($data['unsecure_base_url'])) {
            $unsecureBaseUrl = $data['unsecure_base_url'];
            $setupModel->setConfigData(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $unsecureBaseUrl);
        }

        if (!empty($data['use_secure'])) {
            $setupModel->setConfigData(Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND, 1);
            $setupModel->setConfigData(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $data['secure_base_url']);
            if (!empty($data['use_secure_admin'])) {
                $setupModel->setConfigData(Mage_Core_Model_Store::XML_PATH_SECURE_IN_ADMINHTML, 1);
            }
        }
        elseif (!empty($data['unsecure_base_url'])) {
            $setupModel->setConfigData(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $unsecureBaseUrl);
        }

        /**
         * Saving locale information into DB
         */
        $locale = $this->getDataModel()->getLocaleData();
        if (!empty($locale['locale'])) {
            $setupModel->setConfigData(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $locale['locale']);
        }
        if (!empty($locale['timezone'])) {
            $setupModel->setConfigData(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $locale['timezone']);
        }
        if (!empty($locale['currency'])) {
            $setupModel->setConfigData(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, $locale['currency']);
            $setupModel->setConfigData(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT, $locale['currency']);
            $setupModel->setConfigData(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_ALLOW, $locale['currency']);
        }

        return $this;
    }

    /**
     * Prepare admin user data in model and validate it.
     * Returns TRUE or array of error messages.
     *
     * @param array $data
     * @return mixed
     */
    public function validateAndPrepareAdministrator($data)
    {
        $user = Mage::getModel('admin/user')
            ->load($data['username'], 'username');
        $user->addData($data);

        $result = $user->validate();
        if (is_array($result)) {
            foreach ($result as $error) {
                $this->getDataModel()->addError($error);
            }
            return $result;
        }
        return $user;
    }

    /**
     * Create admin user.
     * Paramater can be prepared user model or array of data.
     * Returns TRUE or throws exception.
     *
     * @param mixed $data
     * @return bool
     */
    public function createAdministrator($data)
    {
        $user = Mage::getModel('admin/user')
            ->load('admin', 'username');
        if ($user && $user->getPassword() == '4297f44b13955235245b2497399d7a93') {
            $user->delete();
        }

        //to support old logic checking if real data was passed
        if (is_array($data)) {
            $data = $this->validateAndPrepareAdministrator($data);
            if (is_array(data)) {
                throw new Exception(Mage::helper('install')->__('Please correct the user data and try again.'));
            }
        }

        //run time flag to force saving entered password
        $data->setForceNewPassword(true);

        $data->save();
        $data->setRoleIds(array(1))->saveRelations();

        /*Mage::getModel("permissions/user")->setRoleId(1)
            ->setUserId($user->getId())
            ->setFirstname($user->getFirstname())
            ->add();*/

        return true;
    }

    /**
     * Validating encryption key.
     * Returns TRUE or array of error messages.
     *
     * @param $key
     * @return unknown_type
     */
    public function validateEncryptionKey($key)
    {
        $errors = array();

        try {
            if ($key) {
                Mage::helper('core')->validateKey($key);
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $this->getDataModel()->addError($e->getMessage());
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Set encryption key
     *
     * @param string $key
     * @return $this
     */
    public function installEnryptionKey($key)
    {
        if ($key) {
            Mage::helper('core')->validateKey($key);
        }
        Mage::getSingleton('install/installer_config')->replaceTmpEncryptKey($key);
        return $this;
    }

    public function finish()
    {
        Mage::getSingleton('install/installer_config')->replaceTmpInstallDate();
        Mage::app()->cleanCache();

        $cacheData = array();
        foreach (Mage::helper('core')->getCacheTypes() as $type => $label) {
            $cacheData[$type] = 1;
        }
        Mage::app()->saveUseCache($cacheData);
        return $this;
    }

}
