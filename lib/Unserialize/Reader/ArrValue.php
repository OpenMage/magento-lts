<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Unserialize_Reader
 */

/**
 * Class Unserialize_Reader_ArrValue
 */
class Unserialize_Reader_ArrValue
{
    /**
     * @var
     */
    public $key;

    /**
     * @var int
     */
    protected $_status;

    /**
     * @object
     */
    protected $_reader;

    public const NOT_STARTED = 1;
    public const READING_VALUE = 2;

    public function __construct($key)
    {
        $this->_status = self::NOT_STARTED;
        $this->key = $key;
    }

    /**
     * @param string $char
     * @param string $prevChar
     * @return mixed|null
     * @throws Exception
     */
    public function read($char, $prevChar)
    {
        if ($this->_status == self::NOT_STARTED) {
            switch ($char) {
                case Unserialize_Parser::TYPE_STRING:
                    $this->_reader = new Unserialize_Reader_Str();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_ARRAY:
                    $this->_reader = new Unserialize_Reader_Arr();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_INT:
                    $this->_reader = new Unserialize_Reader_Int();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_BOOL:
                    $this->_reader = new Unserialize_Reader_Bool();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_DOUBLE:
                    $this->_reader = new Unserialize_Reader_Dbl();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_NULL:
                    $this->_reader = new Unserialize_Reader_Null();
                    $this->_status = self::READING_VALUE;
                    break;
                default:
                    throw new Exception('Unsupported data type ' . $char);
            }
        }

        if ($this->_status == self::READING_VALUE) {
            $value = $this->_reader->read($char, $prevChar);
            if (!is_null($value)) {
                return $value;
            }
        }
        return null;
    }
}
