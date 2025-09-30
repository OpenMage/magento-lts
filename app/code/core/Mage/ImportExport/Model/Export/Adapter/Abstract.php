<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Abstract adapter model
 *
 * @package    Mage_ImportExport
 *
 * @property resource $_fileHandler
 * @property string $_delimiter
 * @property string $_enclosure
 * @property string $_escape
 */
abstract class Mage_ImportExport_Model_Export_Adapter_Abstract
{
    /**
     * Destination file path.
     *
     * @var string
     */
    protected $_destination;

    /**
     * Header columns names.
     *
     * @var array
     */
    protected $_headerCols = null;

    /**
     * Count of rows
     *
     * @var int
     */
    protected $_rowsCount = 0;

    /**
     * Adapter object constructor.
     *
     * @param string $destination OPTIONAL Destination file path.
     * @throws Exception
     */
    final public function __construct($destination = null)
    {
        register_shutdown_function([$this, 'destruct']);

        if (!$destination) {
            $destination = tempnam(sys_get_temp_dir(), 'importexport_');
        }
        if (!is_string($destination)) {
            Mage::throwException(Mage::helper('importexport')->__('Destination file path must be a string'));
        }
        $pathinfo = pathinfo($destination);

        if (empty($pathinfo['dirname']) || !is_writable($pathinfo['dirname'])) {
            Mage::throwException(Mage::helper('importexport')->__('Destination directory is not writable'));
        }
        if (is_file($destination) && !is_writable($destination)) {
            Mage::throwException(Mage::helper('importexport')->__('Destination file is not writable'));
        }
        $this->_destination = $destination;

        $this->_init();
    }

    /**
     * Destruct method on shutdown
     */
    public function destruct() {}

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        return $this;
    }

    /**
     * Get contents of export file.
     *
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->_destination);
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/octet-stream';
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return '';
    }

    /**
     * Get count of wrote lines
     *
     * @return int
     */
    public function getRowsCount()
    {
        return $this->_rowsCount;
    }

    /**
     * Set column names.
     *
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    public function setHeaderCols(array $headerCols)
    {
        if ($this->_headerCols !== null) {
            Mage::throwException(Mage::helper('importexport')->__('Header column names already set'));
        }
        if ($headerCols) {
            foreach ($headerCols as $colName) {
                $this->_headerCols[$colName] = false;
            }
            fputcsv($this->_fileHandler, array_keys($this->_headerCols), $this->_delimiter, $this->_enclosure, $this->_escape);
        }
        return $this;
    }

    /**
     * Returns destination path
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    /**
     * Write row data to source file.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    abstract public function writeRow(array $rowData);
}
