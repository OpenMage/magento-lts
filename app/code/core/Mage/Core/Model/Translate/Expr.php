<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Translate expression object
 *
 * @category   Mage
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
