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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration options storage and logic
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Config_Options extends Varien_Object
{
    public function getDir($type)
    {
        $method = 'get'.ucwords($type).'Dir';
        $dir = $this->$method();
        if (!$dir) {
            throw Mage::exception('Mage_Core', 'Invalid dir type requested: '.$type);
        }
        return $dir;
    }

    public function getAppDir()
    {
        return $this->getDataSetDefault('app_dir', Mage::getRoot());
    }

    public function getBaseDir()
    {
        return $this->getDataSetDefault('base_dir', dirname($this->getAppDir()));
    }

    public function getCodeDir()
    {
        return $this->getDataSetDefault('code_dir', $this->getAppDir().DS.'code');
    }

    public function getDesignDir()
    {
        return $this->getDataSetDefault('design_dir', $this->getAppDir().DS.'design');
    }

    public function getEtcDir()
    {
        return $this->getDataSetDefault('etc_dir', $this->getAppDir().DS.'etc');
    }

    public function getLibDir()
    {
        return $this->getDataSetDefault('lib_dir', $this->getBaseDir().DS.'lib');
    }

    public function getLocaleDir()
    {
        return $this->getDataSetDefault('locale_dir', $this->getAppDir().DS.'locale');
    }

    public function getMediaDir()
    {
        return $this->getDataSetDefault('media_dir', $this->getBaseDir().DS.'media');
    }

    public function getSkinDir()
    {
        return $this->getDataSetDefault('skin_dir', $this->getBaseDir().DS.'skin');
    }

    public function getSysTmpDir()
    {
        return (!empty($_ENV['TMP']) ? $_ENV['TMP'] : DS.'tmp');
    }

    public function getVarDir()
    {
        $dir = $this->getDataSetDefault('var_dir', $this->getBaseDir().DS.'var');
        if (!Mage::getConfig()->createDirIfNotExists($dir)) {
            $dir = $this->getSysTmpDir().DS.'magento'.DS.'var';
            if (!Mage::getConfig()->createDirIfNotExists($dir)) {
                throw new Mage_Core_Exception('Unable to find writable var_dir');
            }
        }
        return $dir;
    }

    public function getTmpDir()
    {
        $dir = $this->getDataSetDefault('tmp_dir', $this->getVarDir().DS.'tmp');
        if (!Mage::getConfig()->createDirIfNotExists($dir)) {
            $dir = $this->getSysTmpDir().DS.'magento'.DS.'tmp';
            if (!Mage::getConfig()->createDirIfNotExists($dir)) {
                throw new Mage_Core_Exception('Unable to find writable tmp_dir');
            }
        }
        return $dir;
    }

    public function getCacheDir()
    {
        $dir = $this->getDataSetDefault('cache_dir', $this->getVarDir().DS.'cache');
        Mage::getConfig()->createDirIfNotExists($dir);
        return $dir;
    }

    public function getLogDir()
    {
        $dir = $this->getDataSetDefault('log_dir', $this->getVarDir().DS.'log');
        Mage::getConfig()->createDirIfNotExists($dir);
        return $dir;
    }

    public function getSessionDir()
    {
        $dir = $this->getDataSetDefault('session_dir', $this->getVarDir().DS.'session');
        Mage::getConfig()->createDirIfNotExists($dir);
        return $dir;
    }

    public function getUploadDir()
    {
        $dir = $this->getDataSetDefault('upload_dir', $this->getMediaDir().DS.'upload');
        Mage::getConfig()->createDirIfNotExists($dir);
        return $dir;
    }

    public function getExportDir()
    {
        $dir = $this->getDataSetDefault('export_dir', $this->getVarDir().DS.'export');
        Mage::getConfig()->createDirIfNotExists($dir);
        return $dir;
    }
}