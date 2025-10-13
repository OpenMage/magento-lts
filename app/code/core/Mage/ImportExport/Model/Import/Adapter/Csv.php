<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * CSV import adapter
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Import_Adapter_Csv extends Mage_ImportExport_Model_Import_Adapter_Abstract
{
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
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    protected function _init()
    {
        $this->_fileHandler = fopen($this->_source, 'r');
        $this->rewind();
        return $this;
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->_currentRow = fgetcsv($this->_fileHandler, 0, $this->_delimiter, $this->_enclosure, $this->_escape);
        $this->_currentKey = $this->_currentRow ? $this->_currentKey + 1 : null;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void Any returned value is ignored.
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        // rewind resource, reset column names, read first row as current
        rewind($this->_fileHandler);
        $this->_colNames = fgetcsv($this->_fileHandler, 0, $this->_delimiter, $this->_enclosure, $this->_escape);
        $this->_currentRow = fgetcsv($this->_fileHandler, 0, $this->_delimiter, $this->_enclosure, $this->_escape);

        if ($this->_currentRow) {
            $this->_currentKey = 0;
        }
    }

    /**
     * Seeks to a position.
     *
     * @param int $position The position to seek to.
     * @throws OutOfBoundsException
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function seek($position)
    {
        if ($position != $this->_currentKey) {
            if ($position == 0) {
                $this->rewind();
                return;
            } elseif ($position > 0) {
                if ($position < $this->_currentKey) {
                    $this->rewind();
                }

                while ($this->_currentRow = fgetcsv($this->_fileHandler, 0, $this->_delimiter, $this->_enclosure, $this->_escape)) {
                    if (++$this->_currentKey == $position) {
                        return;
                    }
                }
            }

            throw new OutOfBoundsException(Mage::helper('importexport')->__('Invalid seek position'));
        }
    }
}
