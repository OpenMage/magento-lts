<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */
abstract class Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_front;

    /**
     * @param Mage_Core_Controller_Varien_Front $front
     * @return $this
     */
    public function setFront($front)
    {
        $this->_front = $front;
        return $this;
    }

    /**
     * @return Mage_Core_Controller_Varien_Front
     */
    public function getFront()
    {
        return $this->_front;
    }

    /**
     * @param string $routeName
     * @return string
     */
    public function getFrontNameByRoute($routeName)
    {
        return $routeName;
    }

    /**
     * @param string $frontName
     * @return string
     */
    public function getRouteByFrontName($frontName)
    {
        return $frontName;
    }

    /**
     * @return bool
     */
    abstract public function match(Zend_Controller_Request_Http $request);
}
