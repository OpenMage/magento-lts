<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Unserialize
 * @package    Unserialize_Reader
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Unserialize_Reader_Null
 */
class Unserialize_Reader_Null
{
    /**
     * @var int
     */
    protected $_status;

    /**
     * @var string
     */
    protected $_value;

    public const NULL_VALUE = 'null';

    public const READING_VALUE = 1;

    /**
     * @param string $char
     * @param string $prevChar
     * @return string|null
     */
    public function read($char, $prevChar)
    {
        if ($prevChar == Unserialize_Parser::SYMBOL_SEMICOLON) {
            $this->_value = self::NULL_VALUE;
            $this->_status = self::READING_VALUE;
            return null;
        }

        if ($this->_status == self::READING_VALUE && $char == Unserialize_Parser::SYMBOL_SEMICOLON) {
            return $this->_value;
        }
        return null;
    }
}
