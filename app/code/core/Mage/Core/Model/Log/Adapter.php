<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log Adapter
 *
 * @category   Mage
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
        } else {
            if (!is_array($data)) {
                $data = [$data];
            }
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
                } else {
                    if (is_array($debugData[$key])) {
                        $debugData[$key] = $this->_filterDebugData($debugData[$key]);
                    }
                }
            }
        }
        return $debugData;
    }
}
