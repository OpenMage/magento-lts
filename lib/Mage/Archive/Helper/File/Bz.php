<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Archive
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
* Helper class that simplifies bz2 files stream reading and writing
*
* @category    Mage
* @package     Mage_Archive
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Mage_Archive_Helper_File_Bz extends Mage_Archive_Helper_File
{
    /**
     * Open bz archive file
     *
     * @throws Mage_Exception
     * @param string $mode
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
