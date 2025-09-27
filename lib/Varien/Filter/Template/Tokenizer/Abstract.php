<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Filter
 */

/**
 * Template constructions tokenizer
 *
 * @package    Varien_Filter
 */

abstract class Varien_Filter_Template_Tokenizer_Abstract
{
    /**
     * Current index in string
     * @var int
     */
    protected $_currentIndex;

    /**
     * String for tokenize
     */
    protected $_string;

    /**
     * Move current index to next char.
     *
     * If index out of bounds returns false
     *
     * @return bool
     */
    public function next()
    {
        if ($this->_currentIndex + 1 >= strlen($this->_string)) {
            return false;
        }

        $this->_currentIndex++;
        return true;
    }

    /**
     * Move current index to previous char.
     *
     * If index out of bounds returns false
     *
     * @return bool
     */
    public function prev()
    {
        if ($this->_currentIndex - 1 < 0) {
            return false;
        }

        $this->_currentIndex--;
        return true;
    }

    /**
     * Return current char
     *
     * @return string
     */
    public function char()
    {
        return $this->_string[$this->_currentIndex];
    }

    /**
     * Set string for tokenize
     */
    public function setString($value)
    {
        $this->_string = $value;
        $this->reset();
    }

    /**
     * Move char index to begin of string
     */
    public function reset()
    {
        $this->_currentIndex = 0;
    }

    /**
     * Return true if current char is white-space
     *
     * @return bool
     */
    public function isWhiteSpace()
    {
        $char = $this->char();
        return trim($char) != $char;
    }

    abstract public function tokenize();
}
