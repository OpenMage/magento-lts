<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Export adapter csv.
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Adapter_Csv extends Mage_ImportExport_Model_Export_Adapter_Abstract
{
    /** config string for escaping export */
    public const CONFIG_ESCAPING_FLAG = 'system/export_csv/escaping';

    /**
     * Field delimiter.
     *
     * @var string
     */
    protected $_delimiter = ',';

    /**
     * Field enclosure character.
     *
     * @var string
     */
    protected $_enclosure = '"';

    /**
     * Field escape character.
     *
     * @var string
     */
    protected $_escape = '\\';

    /**
     * Source file handler.
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * Close file handler on shutdown
     */
    public function destruct()
    {
        if (is_resource($this->_fileHandler)) {
            fclose($this->_fileHandler);
        }
    }

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        $this->_fileHandler = fopen($this->_destination, 'w');
        return $this;
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'text/csv';
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return 'csv';
    }

    /**
     * Write row data to source file.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     * @throws Exception
     */
    public function writeRow(array $rowData)
    {
        if ($this->_headerCols === null) {
            $this->setHeaderCols(array_keys($rowData));
        }

        /**
         * Security enhancement for CSV data processing by Excel-like applications.
         * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1054702
         */
        $data = array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols));
        $data = Mage::helper('core')->getEscapedCSVData($data);

        fputcsv(
            $this->_fileHandler,
            $data,
            $this->_delimiter,
            $this->_enclosure,
            $this->_escape,
        );

        $this->_rowsCount++;

        return $this;
    }
}
