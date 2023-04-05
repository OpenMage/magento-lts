<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template constructions tokenizer
 *
 * @category   Varien
 * @package    Varien_Filter
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @return boolean
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
     * Move current index to previus char.
     *
     * If index out of bounds returns false
     *
     * @return boolean
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
     * @return boolean
     */
    public function isWhiteSpace()
    {
        return trim($this->char()) != $this->char();
    }

    abstract public function tokenize();
}
