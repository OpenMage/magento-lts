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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DB Installer
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Db extends Mage_Install_Model_Installer_Abstract
{
    /**
     * @var database resource
     */
    protected $_dbResource;

    /**
     * Check database connection
     * and return checked connection data
     *
     * @param array $data
     * @return array
     */
    public function checkDbConnectionData($data)
    {
        $data = $this->_getCheckedData($data);

        try {
            $dbModel = ($data['db_model']);

            if (!$resource = $this->_getDbResource($dbModel)) {
                Mage::throwException(Mage::helper('install')->__('No resource for %s DB model.', $dbModel));
            }

            $resource->setConfig($data);

            // check required extensions
            $absenteeExtensions = array();
            $extensions = $resource->getRequiredExtensions();
            foreach ($extensions as $extName) {
                if (!extension_loaded($extName)) {
                    $absenteeExtensions[] = $extName;
                }
            }
            if (!empty($absenteeExtensions)) {
                Mage::throwException(
                    Mage::helper('install')->__('PHP Extensions "%s" must be loaded.', implode(',', $absenteeExtensions))
                );
            }

            $version    = $resource->getVersion();
            $requiredVersion = (string) Mage::getConfig()
                ->getNode(sprintf('install/databases/%s/min_version', $dbModel));

            // check DB server version
            if (version_compare($version, $requiredVersion) == -1) {
                Mage::throwException(
                    Mage::helper('install')->__('The database server version doesn\'t match system requirements (required: %s, actual: %s).', $requiredVersion, $version)
                );
            }

            // check InnoDB support
            if (!$resource->supportEngine()) {
                Mage::throwException(
                    Mage::helper('install')->__('Database server does not support the InnoDB storage engine.')
                );
            }

            // TODO: check user roles
        }
        catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('install')->__($e->getMessage()));
        }
        catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('install')->__('Database connection error.'));
        }

        return $data;
    }

    /**
     * Check database connection data
     *
     * @param  array $data
     * @return array
     */
    protected function _getCheckedData($data)
    {
        if (!isset($data['db_name']) || empty($data['db_name'])) {
            Mage::throwException(Mage::helper('install')->__('Database Name cannot be empty.'));
        }
        //make all table prefix to lower letter
        if ($data['db_prefix'] != '') {
           $data['db_prefix'] = strtolower($data['db_prefix']);
        }
        //check table prefix
        if ($data['db_prefix'] != '') {
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $data['db_prefix'])) {
                Mage::throwException(
                    Mage::helper('install')->__('The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_), the first character should be a letter.')
                );
            }
        }
        //set default db model
        if (!isset($data['db_model']) || empty($data['db_model'])) {
            $data['db_model'] = Mage::getConfig()
                ->getResourceConnectionConfig(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE)->model;
        }
        //set db type according the db model
        if (!isset($data['db_type'])) {
            $data['db_type'] = (string) Mage::getConfig()
                ->getNode(sprintf('install/databases/%s/type', $data['db_model']));
        }

        $dbResource = $this->_getDbResource($data['db_model']);
        $data['db_pdo_type'] = $dbResource->getPdoType();

        if (!isset($data['db_init_statemants'])) {
            $data['db_init_statemants'] = (string) Mage::getConfig()
                ->getNode(sprintf('install/databases/%s/initStatements', $data['db_model']));
        }

        return $data;
    }

    /**
     * Retrieve the database resource
     *
     * @param  string $model database type
     * @return Mage_Install_Model_Installer_Db_Abstract
     */
    protected function _getDbResource($model)
    {
        if (!isset($this->_dbResource)) {
            $resource =  Mage::getSingleton(sprintf('install/installer_db_%s', $model));
            if (!$resource) {
                Mage::throwException(
                    Mage::helper('install')->__('Installer does not exist for %s database type', $model)
                );
            }
            $this->_dbResource = $resource;
        }
        return $this->_dbResource;
    }

    /**
     * Retrieve Connection Type
     *
     * @return string
     *
     * @deprecated since 1.5.0.0
     */
    protected function _getConnenctionType()
    {
        return (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/type');
    }


    /**
     * Check database connection
     *
     * @param array $data
     *
     * @deprecated since 1.5.0.0
     */
    public function checkDatabase($data)
    {
        $this->checkDbConnectionData($data);
    }
}
