<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Unserialize
 * @package     Unserialize_Reader
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @var string|int
     */
    protected $_length = '';

    /**
     * @var int|null
     */
    protected $_status = null;

    /**
     * @var object
     */
    protected $_reader = null;

    const READING_LENGTH = 1;
    const FINISHED_LENGTH = 2;
    const READING_KEY = 3;
    const READING_VALUE = 4;
    const FINISHED_ARR = 5;

    /**
     * @param $char
     * @param $prevChar
     * @return array|null
     * @throws Exception
     */
    public function read($char, $prevChar)
    {
        $this->_result = !is_null($this->_result) ? $this->_result : array();

        if (is_null($this->_status) && $prevChar == Unserialize_Parser::SYMBOL_COLON) {
            $this->_length .= $char;
            $this->_status = self::READING_LENGTH;
            return null;
        }

        if ($this->_status == self::READING_LENGTH) {
            if ($char == Unserialize_Parser::SYMBOL_COLON) {
                $this->_length = (int)$this->_length;
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
                $this->_result[$this->_reader->key] = $value;
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
