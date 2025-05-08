<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Archive
 */

/**
* Helper class that simplifies bz2 files stream reading and writing
*
* @category    Mage
* @package     Mage_Archive
*/
class Mage_Archive_Helper_File_Bz extends Mage_Archive_Helper_File
{
    /**
     * Open bz archive file
     *
     * @throws Mage_Exception
     * @param string $mode
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @bzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new Mage_Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * Write data to bz archive
     *
     * @throws Mage_Exception
     * @param $data
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _write($data)
    {
        $result = @bzwrite($this->_fileHandler, $data);

        if (false === $result) {
            throw new Mage_Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * Read data from bz archive
     *
     * @throws Mage_Exception
     * @param int $length
     * @return string
     */
    protected function _read($length)
    {
        $data = bzread($this->_fileHandler, $length);

        if (false === $data) {
            throw new Mage_Exception('Failed to read data from ' . $this->_filePath);
        }

        return $data;
    }

    /**
     * Close bz archive
     */
    protected function _close()
    {
        bzclose($this->_fileHandler);
    }
}
