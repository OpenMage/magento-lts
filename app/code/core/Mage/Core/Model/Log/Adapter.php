<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Log Adapter
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Log_Adapter
{
    /**
     * Store log file name
     *
     * @var string
     */
    protected $_logFileName = '';

    /**
     * Data to log
     *
     * @var array
     */
    protected $_data = [];

    /**
     * Fields that should be replaced in debug data with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = [];

    /**
     * Set log file name
     *
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->_logFileName = $fileName;
    }

    /**
     * Perform forced log data to file
     *
     * @param mixed $data
     * @return $this
     */
    public function log($data = null)
    {
        if ($data === null) {
            $data = $this->_data;
        } elseif (!is_array($data)) {
            $data = [$data];
        }
        $data = $this->_filterDebugData($data);
        $data['__pid'] = getmypid();
        Mage::log($data, null, $this->_logFileName, true);
        return $this;
    }

    /**
     * Log data setter
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     * @todo replace whole data
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * Setter for private data keys, that should be replaced in debug data with '***'
     *
     * @param array $keys
     * @return $this
     */
    public function setFilterDataKeys($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $this->_debugReplacePrivateDataKeys = $keys;
        return $this;
    }

    /**
     * Recursive filter data by private conventions
     *
     * @param mixed $debugData
     * @return mixed
     */
    protected function _filterDebugData($debugData)
    {
        if (is_array($debugData) && is_array($this->_debugReplacePrivateDataKeys)) {
            foreach (array_keys($debugData) as $key) {
                if (in_array($key, $this->_debugReplacePrivateDataKeys)) {
                    $debugData[$key] = '****';
                } elseif (is_array($debugData[$key])) {
                    $debugData[$key] = $this->_filterDebugData($debugData[$key]);
                }
            }
        }
        return $debugData;
    }
}
