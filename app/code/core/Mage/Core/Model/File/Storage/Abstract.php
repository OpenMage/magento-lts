<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Abstract file storage model class
 *
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_File_Storage_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Store media base directory path
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * Retrieve media base directory path
     *
     * @return string
     */
    public function getMediaBaseDirectory()
    {
        if ($this->_mediaBaseDirectory === null) {
            /** @var Mage_Core_Helper_File_Storage_Database $helper */
            $helper = Mage::helper('core/file_storage_database');
            $this->_mediaBaseDirectory = $helper->getMediaBaseDir();
        }

        return $this->_mediaBaseDirectory;
    }

    /**
     * Collect file info
     *
     * Return array(
     *  filename    => string
     *  content     => string|bool
     *  update_time => string
     *  directory   => string
     * )
     *
     * @param  string $path
     * @return array
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function collectFileInfo($path)
    {
        $path = ltrim($path, '\\/');
        $fullPath = $this->getMediaBaseDirectory() . DS . $path;
        $io = new Varien_Io_File();
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            Mage::throwException(Mage::helper('core')->__('File %s does not exist', $io->getFilteredPath($fullPath)));
        }
        if (!is_readable($fullPath)) {
            Mage::throwException(Mage::helper('core')->__('File %s is not readable', $io->getFilteredPath($fullPath)));
        }

        $path = str_replace(['/', '\\'], '/', $path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = null;
        }

        return [
            'filename'      => basename($path),
            'content'       => @file_get_contents($fullPath),
            'update_time'   => Mage::getSingleton('core/date')->date(),
            'directory'     => $directory,
        ];
    }
}
