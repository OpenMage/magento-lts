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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract installer model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Abstract
{
    /**
     * Installer singleton
     *
     * @var Mage_Install_Model_Installer
     */
    protected $_installer;

    /**
     * Get installer singleton
     *
     * @return Mage_Install_Model_Installer
     */
    protected function _getInstaller()
    {
        if (is_null($this->_installer)) {
            $this->_installer = Mage::getSingleton('install/installer');
        }
        return $this->_installer;
    }

    /**
     * Validate session storage value (files or db)
     * If empty, will return 'files'
     *
     * @param string $value
     * @return string
     * @throws Exception
     */
    protected function _checkSessionSave($value)
    {
        if (empty($value)) {
            return 'files';
        }
        if (!in_array($value, array('files', 'db'), true)) {
            throw new Exception('session_save value must be "files" or "db".');
        }
        return $value;
    }

    /**
     * Validate admin frontname value.
     * If empty, "admin" will be returned
     *
     * @param string $value
     * @return string
     * @throws Exception
     */
    protected function _checkAdminFrontname($value)
    {
        if (empty($value)) {
            return 'admin';
        }
        if (!preg_match('/^[a-z]+[a-z0-9_]+$/i', $value)) {
            throw new Exception('admin_frontname value must contain only letters (a-z or A-Z), numbers (0-9) or underscore(_), first character should be a letter.');
        }
        return $value;
    }
}
