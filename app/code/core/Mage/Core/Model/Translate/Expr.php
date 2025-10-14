<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Translate expression object
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Translate_Expr
{
    protected $_text;

    protected $_module;

    /**
     * @param string $text
     * @param string $module
     */
    public function __construct($text = '', $module = '')
    {
        $this->_text    = $text;
        $this->_module  = $module;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->_text = $text;
        return $this;
    }

    /**
     * @param string $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * Retrieve expression text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Retrieve expression module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Retrieve expression code
     *
     * @param   string $separator
     * @return  string
     */
    public function getCode($separator = '::')
    {
        return $this->getModule() . $separator . $this->getText();
    }
}
