<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract installer model
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Abstract
{
    /**
     * Installer singleton
     *
     * @var Mage_Install_Model_Installer|null
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
        if (!in_array($value, ['files', 'db'], true)) {
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
