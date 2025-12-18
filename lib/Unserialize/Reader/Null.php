<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Unserialize_Reader
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
     * @param  string      $char
     * @param  string      $prevChar
     * @return null|string
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
