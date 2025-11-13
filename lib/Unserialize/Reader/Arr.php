<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Unserialize_Reader
 */

/**
 * Class Unserialize_Reader_Arr
 */
class Unserialize_Reader_Arr
{
    /**
     * @var array
     */
    protected $_result = null;

    /**
     * @var int|string
     */
    protected $_length = '';

    /**
     * @var null|int
     */
    protected $_status = null;

    /**
     * @var object
     */
    protected $_reader = null;

    public const READING_LENGTH = 1;

    public const FINISHED_LENGTH = 2;

    public const READING_KEY = 3;

    public const READING_VALUE = 4;

    public const FINISHED_ARR = 5;

    /**
     * @param $char
     * @param $prevChar
     * @return null|array
     * @throws Exception
     */
    public function read($char, $prevChar)
    {
        $this->_result = !is_null($this->_result) ? $this->_result : [];

        if (is_null($this->_status) && $prevChar == Unserialize_Parser::SYMBOL_COLON) {
            $this->_length .= $char;
            $this->_status = self::READING_LENGTH;
            return null;
        }

        if ($this->_status == self::READING_LENGTH) {
            if ($char == Unserialize_Parser::SYMBOL_COLON) {
                $this->_length = (int) $this->_length;
                if ($this->_length == 0) {
                    $this->_status = self::FINISHED_ARR;
                    return null;
                }

                $this->_status = self::FINISHED_LENGTH;
            } else {
                $this->_length .= $char;
            }
        }

        if ($this->_status == self::FINISHED_LENGTH && $prevChar == '{') {
            $this->_reader = new Unserialize_Reader_ArrKey();
            $this->_status = self::READING_KEY;
        }

        if ($this->_status == self::READING_KEY) {
            $key = $this->_reader->read($char, $prevChar);
            if (!is_null($key)) {
                $this->_status = self::READING_VALUE;
                $this->_reader = new Unserialize_Reader_ArrValue($key);
                return null;
            }
        }

        if ($this->_status == self::READING_VALUE) {
            $value = $this->_reader->read($char, $prevChar);
            if (!is_null($value)) {
                $this->_result[$this->_reader->key]
                    = ($value == Unserialize_Reader_Null::NULL_VALUE && $prevChar == Unserialize_Parser::TYPE_NULL)
                        ? null
                        : $value;
                if (count($this->_result) < $this->_length) {
                    $this->_reader = new Unserialize_Reader_ArrKey();
                    $this->_status = self::READING_KEY;
                    return null;
                } else {
                    $this->_status = self::FINISHED_ARR;
                    return null;
                }
            }
        }

        if ($this->_status == self::FINISHED_ARR) {
            if ($char == '}') {
                return $this->_result;
            }
        }
    }
}
