<?php
/**
 * Class Unserialize_Reader_Str
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
/**
 * @package    Unserialize_Reader
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
                $this->_length = (int)$this->_length;
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
                    return (string)$this->_value;
                }
            }
        }
        return null;
    }
}
