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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract file storage model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
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
        if (null === $this->_mediaBaseDirectory) {
            /** @var $helper Mage_Core_Helper_File_Storage_Database */
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

        $path = str_replace(array('/', '\\'), '/', $path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = null;
        }

        return array(
            'filename'      => basename($path),
            'content'       => @file_get_contents($fullPath),
            'update_time'   => Mage::getSingleton('core/date')->date(),
            'directory'     => $directory
        );
    }
}
