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
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Console installer
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Console extends Mage_Install_Model_Installer_Abstract
{
    /**
     * Available options
     *
     * @var array
     */
    protected $_options;

    /**
     * Script arguments
     *
     * @var array
     */
    protected $_args = [];

    /**
     * Installer data model to store data between installations steps
     *
     * @var Mage_Install_Model_Installer_Data|Mage_Install_Model_Session
     */
    protected $_dataModel;

    /**
     * Current application
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Get available options list
     *
     * @return array
     */
    protected function _getOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = [
                'license_agreement_accepted'    => ['required' => true, 'comment' => ''],
                'locale'              => ['required' => true, 'comment' => ''],
                'timezone'            => ['required' => true, 'comment' => ''],
                'default_currency'    => ['required' => true, 'comment' => ''],
                'db_model'            => ['comment' => ''],
                'db_host'             => ['required' => true, 'comment' => ''],
                'db_name'             => ['required' => true, 'comment' => ''],
                'db_user'             => ['required' => true, 'comment' => ''],
                'db_pass'             => ['comment' => ''],
                'db_prefix'           => ['comment' => ''],
                'url'                 => ['required' => true, 'comment' => ''],
                'skip_url_validation' => ['comment' => ''],
                'use_rewrites'      => ['required' => true, 'comment' => ''],
                'use_secure'        => ['required' => true, 'comment' => ''],
                'secure_base_url'   => ['required' => true, 'comment' => ''],
                'use_secure_admin'  => ['required' => true, 'comment' => ''],
                'admin_lastname'    => ['required' => true, 'comment' => ''],
                'admin_firstname'   => ['required' => true, 'comment' => ''],
                'admin_email'       => ['required' => true, 'comment' => ''],
                'admin_username'    => ['required' => true, 'comment' => ''],
                'admin_password'    => ['required' => true, 'comment' => ''],
                'encryption_key'    => ['comment' => ''],
                'session_save'      => ['comment' => ''],
                'admin_frontname'   => ['comment' => ''],
                'enable_charts'     => ['comment' => ''],
            ];
        }
        return $this->_options;
    }

    /**
     * Set and validate arguments
     *
     * @param array $args
     * @return bool
     */
    public function setArgs($args = null)
    {
        if (empty($args)) {
            // take server args
            $args = $_SERVER['argv'];
        }

        /**
         * Parse arguments
         */
        $currentArg = false;
        $match = false;
        foreach ($args as $arg) {
            if (preg_match('/^--(.*)$/', $arg, $match)) {
                // argument name
                $currentArg = $match[1];
                // in case if argument doen't need a value
                $args[$currentArg] = true;
            } else {
                // argument value
                if ($currentArg) {
                    $args[$currentArg] = $arg;
                }
                $currentArg = false;
            }
        }

        if (isset($args['get_options'])) {
            $this->printOptions();
            return false;
        }

        /**
         * Check required arguments
         */
        foreach ($this->_getOptions() as $name => $option) {
            if (isset($option['required']) && $option['required'] && !isset($args[$name])) {
                $error = 'ERROR: ' . 'You should provide the value for --' . $name . ' parameter';
                if (!empty($option['comment'])) {
                    $error .= ': ' . $option['comment'];
                }
                $this->addError($error);
            }
        }

        if ($this->hasErrors()) {
            return false;
        }

        /**
         * Validate license agreement acceptance
         */
        if (!$this->_checkFlag($args['license_agreement_accepted'])) {
            $this->addError(
                'ERROR: You have to accept Magento license agreement terms and conditions to continue installation'
            );
            return false;
        }

        /**
         * Set args values
         */
        foreach ($this->_getOptions() as $name => $option) {
            $this->_args[$name] = $args[$name] ?? '';
        }

        return true;
    }

    /**
     * Add error
     *
     * @param string $error
     * @return $this
     */
    public function addError($error)
    {
        $this->_getDataModel()->addError($error);
        return $this;
    }

    /**
     * Check if there were any errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (count($this->_getDataModel()->getErrors()) > 0);
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_getDataModel()->getErrors();
    }

    /**
     * Check flag value
     *
     * Returns true for 'yes', 1, 'true'
     * Case insensitive
     *
     * @param string $value
     * @return bool
     */
    protected function _checkFlag($value)
    {
        return ($value == 1)
            || preg_match('/^(yes|y|true)$/i', $value);
    }

    /**
     * Get data model (used to store data between installation steps
     *
     * @return Mage_Install_Model_Installer_Data
     */
    protected function _getDataModel()
    {
        if (is_null($this->_dataModel)) {
            $this->_dataModel = Mage::getModel('install/installer_data');
        }
        return $this->_dataModel;
    }

    /**
     * Get encryption key from data model
     *
     * @return string
     */
    public function getEncryptionKey()
    {
        return $this->_getDataModel()->getEncryptionKey();
    }

    /**
     * Init installation
     *
     * @param Mage_Core_Model_App $app
     * @return bool
     */
    public function init(Mage_Core_Model_App $app)
    {
        $this->_app = $app;
        $this->_getInstaller()->setDataModel($this->_getDataModel());

        /**
         * Check if already installed
         */
        if (Mage::isInstalled()) {
            $this->addError('ERROR: Magento is already installed');
            return false;
        }

        return true;
    }

    /**
     * Prepare data ans save it in data model
     *
     * @return $this
     */
    protected function _prepareData()
    {
        /**
         * Locale settings
         */
        $this->_getDataModel()->setLocaleData([
            'locale'            => $this->_args['locale'],
            'timezone'          => $this->_args['timezone'],
            'currency'          => $this->_args['default_currency'],
        ]);

        /**
         * Database and web config
         */
        $this->_getDataModel()->setConfigData([
            'db_model'            => $this->_args['db_model'],
            'db_host'             => $this->_args['db_host'],
            'db_name'             => $this->_args['db_name'],
            'db_user'             => $this->_args['db_user'],
            'db_pass'             => $this->_args['db_pass'],
            'db_prefix'           => $this->_args['db_prefix'],
            'use_rewrites'        => $this->_checkFlag($this->_args['use_rewrites']),
            'use_secure'          => $this->_checkFlag($this->_args['use_secure']),
            'unsecure_base_url'   => $this->_args['url'],
            'secure_base_url'     => $this->_args['secure_base_url'],
            'use_secure_admin'    => $this->_checkFlag($this->_args['use_secure_admin']),
            'session_save'        => $this->_checkSessionSave($this->_args['session_save']),
            'admin_frontname'     => $this->_checkAdminFrontname($this->_args['admin_frontname']),
            'skip_url_validation' => $this->_checkFlag($this->_args['skip_url_validation']),
            'enable_charts'       => $this->_checkFlag($this->_args['enable_charts']),
        ]);

        /**
         * Primary admin user
         */
        $this->_getDataModel()->setAdminData([
            'firstname'         => $this->_args['admin_firstname'],
            'lastname'          => $this->_args['admin_lastname'],
            'email'             => $this->_args['admin_email'],
            'username'          => $this->_args['admin_username'],
            'new_password'      => $this->_args['admin_password'],
        ]);

        return $this;
    }

    /**
     * Install Magento
     *
     * @return bool
     */
    public function install()
    {
        try {

            /**
             * Check if already installed
             */
            if (Mage::isInstalled()) {
                $this->addError('ERROR: Magento is already installed');
                return false;
            }

            /**
             * Skip URL validation, if set
             */
            $this->_getDataModel()->setSkipUrlValidation($this->_args['skip_url_validation']);
            $this->_getDataModel()->setSkipBaseUrlValidation($this->_args['skip_url_validation']);

            /**
             * Prepare data
             */
            $this->_prepareData();

            if ($this->hasErrors()) {
                return false;
            }

            $installer = $this->_getInstaller();

            /**
             * Install configuration
             */
            $installer->installConfig($this->_getDataModel()->getConfigData());

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Reinitialize configuration (to use new config data)
             */

            $this->_app->cleanCache();
            Mage::getConfig()->reinit();

            /**
             * Install database
             */
            $installer->installDb();

            if ($this->hasErrors()) {
                return false;
            }

            // apply data updates
            Mage_Core_Model_Resource_Setup::applyAllDataUpdates();

            /**
             * Validate entered data for administrator user
             */
            $user = $installer->validateAndPrepareAdministrator($this->_getDataModel()->getAdminData());

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Prepare encryption key and validate it
             */
            $encryptionKey = empty($this->_args['encryption_key'])
                ? md5(Mage::helper('core')->getRandomString(10))
                : $this->_args['encryption_key'];
            $this->_getDataModel()->setEncryptionKey($encryptionKey);
            $installer->validateEncryptionKey($encryptionKey);

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Create primary administrator user
             */
            $installer->createAdministrator($user);

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Save encryption key or create if empty
             */
            $installer->installEnryptionKey($encryptionKey);

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Installation finish
             */
            $installer->finish();

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Change directories mode to be writable by apache user
             */
            @chmod('var/cache', 0777);
            @chmod('var/session', 0777);
        } catch (Exception $e) {
            $this->addError('ERROR: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Print available currency, locale and timezone options
     *
     * @return $this
     */
    public function printOptions()
    {
        $options = [
            'locale'    => $this->_app->getLocale()->getOptionLocales(),
            'currency'  => $this->_app->getLocale()->getOptionCurrencies(),
            'timezone'  => $this->_app->getLocale()->getOptionTimezones(),
        ];
        var_export($options);
        return $this;
    }

    /**
     * Check if installer is run in shell, and redirect if run on web
     *
     * @param string $url fallback url to redirect to
     * @return bool
     */
    public function checkConsole($url = null)
    {
        if (defined('STDIN') && defined('STDOUT') && (defined('STDERR'))) {
            return true;
        }
        if (is_null($url)) {
            $url = preg_replace('/install\.php/i', '', Mage::getBaseUrl());
            $url = preg_replace('/\/\/$/', '/', $url);
        }
        header('Location: ' . $url);
        return false;
    }
}
