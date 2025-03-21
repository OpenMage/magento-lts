<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API exception
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Exception extends Exception
{
    /**
     * Log the exception in the log file?
     * @var bool
     */
    protected $_shouldLog = true;

    /**
     * Exception constructor
     *
     * @param string $message
     * @param int $code
     * @param bool $shouldLog
     */
    public function __construct($message, $code, $shouldLog = true)
    {
        if ($code <= 100 || $code >= 599) {
            throw new Exception(sprintf('Invalid Exception code "%d"', $code));
        }

        $this->_shouldLog = $shouldLog;
        parent::__construct($message, $code);
    }

    /**
     * Check if exception should be logged
     *
     * @return bool
     */
    public function shouldLog()
    {
        return $this->_shouldLog;
    }
}
