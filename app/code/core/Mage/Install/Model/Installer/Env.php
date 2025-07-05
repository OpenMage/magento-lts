<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Environment installer
 *
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Env extends Mage_Install_Model_Installer_Abstract
{
    public function __construct() {}

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
                    Mage::helper('install')->__('One of PHP Extensions "%s" must be loaded.', implode(',', $extension)),
                );
                return false;
            }
        } elseif (!extension_loaded($extension)) {
            Mage::getSingleton('install/session')->addError(
                Mage::helper('install')->__('PHP extension "%s" must be loaded.', $extension),
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
