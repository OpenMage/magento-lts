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
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
     * Check database connection
     *
     * $data = array(
     *      [db_host]
     *      [db_name]
     *      [db_user]
     *      [db_pass]
     * )
     *
     * @param array $data
     */
    public function checkDatabase($data)
    {
        $config = array(
            'host'      => $data['db_host'],
            'username'  => $data['db_user'],
            'password'  => $data['db_pass'],
            'dbname'    => $data['db_name']
        );

        try {
            $connection = Mage::getSingleton('core/resource')->createConnection('install', $this->_getConnenctionType(), $config);
            $result = $connection->query($connection->quoteInto('SHOW VARIABLES LIKE ?', 'version'));
            $row = $result->fetch();
            $version = $row['Value'];
            preg_match("([0-9.]+)",$version,$toCompare);
            $requiredVersion = (string)Mage::getSingleton('install/config')->getNode('check/mysql/version');

            if (version_compare(isset($toCompare[0])?$toCompare[0]:$version, $requiredVersion) == -1) {
                Mage::throwException(Mage::helper('install')->__('Database server version does not match system requirements (required: %s, actual: %s)', $requiredVersion, $version));
            }
        }
        catch (Exception $e){
            $this->_getInstaller()->getDataModel()->addError($e->getMessage());
            Mage::throwException(Mage::helper('install')->__('Database connection error'));
        }
    }

    protected function _getConnenctionType()
    {
        return (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/type');
    }
}