<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Filesystem installer
 *
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Filesystem extends Mage_Install_Model_Installer_Abstract
{
    /**
     * @deprecated since 1.7.1.0
     */
    public const MODE_WRITE = 'write';

    /**
     * @deprecated since 1.7.1.0
     */
    public const MODE_READ  = 'read';

    public function __construct() {}

    /**
     * Check and prepare file system
     */
    public function install()
    {
        if (!$this->_checkFilesystem()) {
            throw new Exception();
        }

        return $this;
    }

    /**
     * Check file system by config
     *
     * @return bool
     */
    protected function _checkFilesystem()
    {
        $res = true;
        $config = Mage::getSingleton('install/config')->getWritableFullPathsForCheck();

        if (is_array($config)) {
            foreach ($config as $item) {
                $recursive = isset($item['recursive']) ? (bool) $item['recursive'] : false;
                $existence = isset($item['existence']) ? (bool) $item['existence'] : false;
                $checkRes = $this->_checkFullPath($item['path'], $recursive, $existence);
                $res = $res && $checkRes;
            }
        }

        return $res;
    }

    /**
     * Check file system path
     *
     * @param  string $path
     * @param  bool   $recursive
     * @param  bool   $existence
     * @param  string $mode
     * @return bool
     * @deprecated since 1.7.1.0
     */
    protected function _checkPath($path, $recursive, $existence, $mode)
    {
        return $this->_checkFullPath(dirname(Mage::getRoot()) . $path, $recursive, $existence);
    }

    /**
     * Check file system full path
     *
     * @param  string $fullPath
     * @param  bool   $recursive
     * @param  bool   $existence
     * @return bool
     */
    protected function _checkFullPath($fullPath, $recursive, $existence)
    {
        $res = true;
        $setError = $existence && (is_dir($fullPath) && !isDirWriteable($fullPath) || !is_writable($fullPath))
            || !$existence && file_exists($fullPath) && !is_writable($fullPath);

        if ($setError) {
            $this->_getInstaller()->getDataModel()->addError(
                Mage::helper('install')->__('Path "%s" must be writable.', $fullPath),
            );
            $res = false;
        }

        if ($recursive && is_dir($fullPath)) {
            $skipFileNames = ['.svn', '.htaccess'];
            foreach (new DirectoryIterator($fullPath) as $file) {
                $fileName = $file->getFilename();
                if (!$file->isDot() && !in_array($fileName, $skipFileNames)) {
                    $res = $this->_checkFullPath($fullPath . DS . $fileName, $recursive, $existence) && $res;
                }
            }
        }

        return $res;
    }
}
