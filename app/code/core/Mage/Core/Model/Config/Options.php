<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration options storage and logic
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Config_Options extends Varien_Object
{
    /**
     * Var directory
     *
     * @var string
     */
    public const VAR_DIRECTORY = 'var';
    /**
     * Flag cache for existing or already created directories
     *
     * @var array
     */
    protected $_dirExists = [];

    /**
     * Initialize default values of the options
     */
    protected function _construct()
    {
        $appRoot = Mage::getRoot();
        $root   = dirname($appRoot);

        $this->_data['app_dir']     = $appRoot;
        $this->_data['base_dir']    = $root;
        $this->_data['code_dir']    = $appRoot . DS . 'code';
        $this->_data['design_dir']  = $appRoot . DS . 'design';
        $this->_data['etc_dir']     = $appRoot . DS . 'etc';
        $this->_data['lib_dir']     = $root . DS . 'lib';
        $this->_data['locale_dir']  = $appRoot . DS . 'locale';
        $this->_data['media_dir']   = $root . DS . 'media';
        $this->_data['skin_dir']    = $root . DS . 'skin';
        $this->_data['var_dir']     = $this->getVarDir();
        $this->_data['tmp_dir']     = $this->_data['var_dir'] . DS . 'tmp';
        $this->_data['cache_dir']   = $this->_data['var_dir'] . DS . 'cache';
        $this->_data['log_dir']     = $this->_data['var_dir'] . DS . 'log';
        $this->_data['session_dir'] = $this->_data['var_dir'] . DS . 'session';
        $this->_data['upload_dir']  = $this->_data['media_dir'] . DS . 'upload';
        $this->_data['export_dir']  = $this->_data['var_dir'] . DS . 'export';
    }

    /**
     * @param string $type
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getDir($type)
    {
        $method = 'get' . ucwords($type) . 'Dir';
        $dir = $this->$method();
        if (!$dir) {
            throw Mage::exception('Mage_Core', 'Invalid dir type requested: ' . $type);
        }
        return $dir;
    }

    /**
     * @return string
     */
    public function getAppDir()
    {
        //return $this->getDataSetDefault('app_dir', Mage::getRoot());
        return $this->_data['app_dir'];
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        //return $this->getDataSetDefault('base_dir', dirname($this->getAppDir()));
        return $this->_data['base_dir'];
    }

    /**
     * @return string
     */
    public function getCodeDir()
    {
        //return $this->getDataSetDefault('code_dir', $this->getAppDir().DS.'code');
        return $this->_data['code_dir'];
    }

    /**
     * @return string
     */
    public function getDesignDir()
    {
        //return $this->getDataSetDefault('design_dir', $this->getAppDir().DS.'design');
        return $this->_data['design_dir'];
    }

    /**
     * @return string
     */
    public function getEtcDir()
    {
        //return $this->getDataSetDefault('etc_dir', $this->getAppDir().DS.'etc');
        return $this->_data['etc_dir'];
    }

    /**
     * @return string
     */
    public function getLibDir()
    {
        //return $this->getDataSetDefault('lib_dir', $this->getBaseDir().DS.'lib');
        return $this->_data['lib_dir'];
    }

    /**
     * @return string
     */
    public function getLocaleDir()
    {
        //return $this->getDataSetDefault('locale_dir', $this->getAppDir().DS.'locale');
        return $this->_data['locale_dir'];
    }

    /**
     * @return string
     */
    public function getMediaDir()
    {
        //return $this->getDataSetDefault('media_dir', $this->getBaseDir().DS.'media');
        return $this->_data['media_dir'];
    }

    /**
     * @return string
     */
    public function getSkinDir()
    {
        //return $this->getDataSetDefault('skin_dir', $this->getBaseDir().DS.'skin');
        return $this->_data['skin_dir'];
    }

    /**
     * @return bool|string
     */
    public function getSysTmpDir()
    {
        return sys_get_temp_dir();
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getVarDir()
    {
        //$dir = $this->getDataSetDefault('var_dir', $this->getBaseDir().DS.'var');
        $dir = $this->_data['var_dir'] ?? ($this->_data['base_dir'] . DS . self::VAR_DIRECTORY);
        if (!$this->createDirIfNotExists($dir)) {
            $dir = $this->getSysTmpDir() . DS . 'magento' . DS . 'var';
            if (!$this->createDirIfNotExists($dir)) {
                throw new Mage_Core_Exception('Unable to find writable var_dir');
            }
        }
        return $dir;
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getTmpDir()
    {
        //$dir = $this->getDataSetDefault('tmp_dir', $this->getVarDir().DS.'tmp');
        $dir = $this->_data['tmp_dir'];
        if (!$this->createDirIfNotExists($dir)) {
            $dir = $this->getSysTmpDir() . DS . 'magento' . DS . 'tmp';
            if (!$this->createDirIfNotExists($dir)) {
                throw new Mage_Core_Exception('Unable to find writable tmp_dir');
            }
        }
        return $dir;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        //$dir = $this->getDataSetDefault('cache_dir', $this->getVarDir().DS.'cache');
        $dir = $this->_data['cache_dir'];
        $this->createDirIfNotExists($dir);
        return $dir;
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        //$dir = $this->getDataSetDefault('log_dir', $this->getVarDir().DS.'log');
        $dir = $this->_data['log_dir'];
        $this->createDirIfNotExists($dir);
        return $dir;
    }

    /**
     * @return string
     */
    public function getSessionDir()
    {
        //$dir = $this->getDataSetDefault('session_dir', $this->getVarDir().DS.'session');
        $dir = $this->_data['session_dir'];
        $this->createDirIfNotExists($dir);
        return $dir;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        //$dir = $this->getDataSetDefault('upload_dir', $this->getMediaDir().DS.'upload');
        $dir = $this->_data['upload_dir'];
        $this->createDirIfNotExists($dir);
        return $dir;
    }

    /**
     * @return string
     */
    public function getExportDir()
    {
        //$dir = $this->getDataSetDefault('export_dir', $this->getVarDir().DS.'export');
        $dir = $this->_data['export_dir'];
        $this->createDirIfNotExists($dir);
        return $dir;
    }

    /**
     * @param string $dir
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function createDirIfNotExists($dir)
    {
        if (!empty($this->_dirExists[$dir])) {
            return true;
        }
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                return false;
            }
            if (!isDirWriteable($dir)) {
                return false;
            }
        } else {
            $oldUmask = umask(0);
            if (!@mkdir($dir, 0777, true)) {
                return false;
            }
            umask($oldUmask);
        }
        $this->_dirExists[$dir] = true;
        return true;
    }
}
