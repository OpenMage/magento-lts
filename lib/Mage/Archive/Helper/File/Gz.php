<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Archive
 */

/**
 * Helper class that simplifies gz files stream reading and writing
 *
 * @category    Mage
 * @package     Mage_Archive
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
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
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
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
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
