<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert exception
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Exception extends Varien_Exception
{
    public const NOTICE = 'NOTICE';
    public const WARNING = 'WARNING';
    public const ERROR = 'ERROR';
    public const FATAL = 'FATAL';

    protected $_container;

    protected $_level;

    protected $_position;

    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    public function getContainer()
    {
        return $this->_container;
    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function setLevel($level)
    {
        $this->_level = $level;
        return $this;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
}
