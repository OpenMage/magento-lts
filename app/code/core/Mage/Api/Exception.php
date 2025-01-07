<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api exception
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Exception extends Mage_Core_Exception
{
    protected $_customMessage = null;

    /**
     * Mage_Api_Exception constructor.
     * @param string $faultCode
     * @param string|null $customMessage
     */
    public function __construct($faultCode, $customMessage = null)
    {
        parent::__construct($faultCode);
        $this->_customMessage = $customMessage;
    }

    /**
     * Custom error message, if error is not in api.
     *
     * @return string
     */
    public function getCustomMessage()
    {
        return $this->_customMessage;
    }
}
