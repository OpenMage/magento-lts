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
 * @package     Mage_Archive
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
* Helper class that simplifies gz files stream reading and writing
*
* @category    Mage
* @package     Mage_Archive
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Mage_Archive_Helper_File_Gz extends Mage_Archive_Helper_File
{

    /**
     * Overwritten Mage_Archive_Helper_File constructor with zlib extension check
     * @param string $filePath
     * @throws Mage_Exception
     */
    public function __construct($filePath)
    {
        if (!function_exists('gzopen')) {
            throw new Mage_Exception('PHP Extensions "zlib" must be loaded.');
        }

        parent::__construct($filePath);
    }

    /**
     * @see Mage_Archive_Helper_File::_open()
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @gzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new Mage_Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * @see Mage_Archive_Helper_File::_write()
     */
    protected function _write($data)
    {
        $result = @gzwrite($this->_fileHandler, $data);

        if (empty($result) && !empty($data)) {
            throw new Mage_Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * @see Mage_Archive_Helper_File::_read()
     */
    protected function _read($length)
    {
        return gzread($this->_fileHandler, $length);
    }

    /**
     * @see Mage_Archive_Helper_File::_eof()
     */
    protected function _eof()
    {
        return gzeof($this->_fileHandler);
    }

    /**
     * @see Mage_Archive_Helper_File::_close()
     */
    protected function _close()
    {
        gzclose($this->_fileHandler);
    }
}
