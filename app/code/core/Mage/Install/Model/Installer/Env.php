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
 * Environment installer
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Env extends Mage_Install_Model_Installer_Abstract
{
    public function __construct()
    {
    }

    public function install()
    {
        if (!$this->_checkPhpExtensions()) {
            throw new Exception();
        }
        return $this;
    }

    protected function _checkPhpExtensions()
    {
        $res = true;
        $config = Mage::getSingleton('install/config')->getExtensionsForCheck();
        foreach ($config as $extension => $info) {
            if (!empty($info) && is_array($info)) {
                $res = $this->_checkExtension($info) && $res;
            } else {
                $res = $this->_checkExtension($extension) && $res;
            }
        }
        return $res;
    }

    protected function _checkExtension($extension)
    {
        if (is_array($extension)) {
            $oneLoaded = false;
            foreach ($extension as $item) {
                if (extension_loaded($item)) {
                    $oneLoaded = true;
                }
            }

            if (!$oneLoaded) {
                Mage::getSingleton('install/session')->addError(
                    Mage::helper('install')->__('One of PHP Extensions "%s" must be loaded.', implode(',', $extension))
                );
                return false;
            }
        } elseif (!extension_loaded($extension)) {
            Mage::getSingleton('install/session')->addError(
                Mage::helper('install')->__('PHP extension "%s" must be loaded.', $extension)
            );
            return false;
        } else {
            /*Mage::getSingleton('install/session')->addError(
                Mage::helper('install')->__("PHP Extension '%s' loaded", $extension)
            );*/
        }
        return true;
    }
}
