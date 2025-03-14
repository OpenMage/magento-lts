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
 * Class Unserialize_Reader_Str
 */
class Unserialize_Reader_Str
{
    /**
     * @var int|null
     */
    protected $_status = null;

    /**
     * @var int|string
     */
    protected $_length;

    /**
     * @var string
     */
    protected $_value;

    public const READING_LENGTH = 1;
    public const FINISHED_LENGTH = 2;
    public const READING_VALUE = 3;

    /**
     * @param string $char
     * @param string $prevChar
     * @return null|string
     */
    public function read($char, $prevChar)
    {
        if (is_null($this->_status) && $prevChar == Unserialize_Parser::SYMBOL_COLON) {
            $this->_status = self::READING_LENGTH;
        }

        if ($this->_status == self::READING_LENGTH) {
            if ($char != Unserialize_Parser::SYMBOL_COLON) {
                $this->_length .= $char;
            } else {
                $this->_length = (int) $this->_length;
                $this->_status = self::FINISHED_LENGTH;
            }
        }

        if ($this->_status == self::FINISHED_LENGTH) {
            if ($char == Unserialize_Parser::SYMBOL_QUOTE) {
                $this->_status = self::READING_VALUE;
                return null;
            }
        }

        if ($this->_status == self::READING_VALUE) {
            if (is_null($this->_value)) {
                $this->_value = '';
            }

            if (strlen($this->_value) < $this->_length) {
                $this->_value .= $char;
                return null;
            }

            if (strlen($this->_value) == $this->_length) {
                if ($char == Unserialize_Parser::SYMBOL_SEMICOLON && $prevChar == Unserialize_Parser::SYMBOL_QUOTE) {
                    return (string) $this->_value;
                }
            }
        }
        return null;
    }
}
