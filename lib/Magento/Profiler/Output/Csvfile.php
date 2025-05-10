<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Magento_Profiler
 */

/**
 * Class that represents profiler output in Html format
 */
class Magento_Profiler_Output_Csvfile extends Magento_Profiler_OutputAbstract
{
    /**
     * @var string
     */
    protected $_filename;

    /**
     * @var string
     */
    protected $_delimiter;

    /**
     * @var string
     */
    protected $_enclosure;

    /**
     * @var string
     */
    protected $_escape = '\\';

    /**
     * Start output buffering
     *
     * @param string      $filename Target file to save CSV data
     * @param string|null $filter Pattern to filter timers by their identifiers (SQL LIKE syntax)
     * @param string      $delimiter Delimiter for CSV format
     * @param string      $enclosure Enclosure for CSV format
     */
    public function __construct($filename, $filter = null, $delimiter = ',', $enclosure = '"')
    {
        parent::__construct($filter);

        $this->_filename = $filename;
        $this->_delimiter = $delimiter;
        $this->_enclosure = $enclosure;
    }

    /**
     * Display profiling results
     */
    public function display()
    {
        $fileHandle = fopen($this->_filename, 'w');
        if (!$fileHandle) {
            throw new Varien_Exception(sprintf('Can not open a file "%s".', $this->_filename));
        }

        $needLock = (!str_starts_with($this->_filename, 'php://'));
        $isLocked = false;
        while ($needLock && !$isLocked) {
            $isLocked = flock($fileHandle, LOCK_EX);
        }

        $this->_writeFileContent($fileHandle);

        if ($isLocked) {
            flock($fileHandle, LOCK_UN);
        }
        fclose($fileHandle);
    }

    /**
     * Write content into an opened file handle
     *
     * @param resource $fileHandle
     */
    protected function _writeFileContent($fileHandle)
    {
        foreach ($this->_getTimers() as $timerId) {
            $row = [];
            foreach ($this->_getColumns() as $columnId) {
                $row[] = $this->_renderColumnValue($timerId, $columnId);
            }
            fputcsv($fileHandle, $row, $this->_delimiter, $this->_enclosure, $this->_escape);
        }
    }
}
