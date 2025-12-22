<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Unserialize_Reader
 */

/**
 * Class Unserialize_Reader_Int
 */
class Unserialize_Reader_Bool
{
    /**
     * @var int
     */
    protected $_status;

    /**
     * @var int|string
     */
    protected $_value;

    public const READING_VALUE = 1;

    /**
     * @param  string   $char
     * @param  string   $prevChar
     * @return null|int
     */
    public function read($char, $prevChar)
    {
        if ($prevChar == Unserialize_Parser::SYMBOL_COLON) {
            $this->_value .= $char;
            $this->_status = self::READING_VALUE;
            return null;
        }

        if ($this->_status == self::READING_VALUE) {
            if ($char !== Unserialize_Parser::SYMBOL_SEMICOLON) {
                $this->_value .= $char;
            } else {
                return (bool) $this->_value;
            }
        }

        return null;
    }
}
