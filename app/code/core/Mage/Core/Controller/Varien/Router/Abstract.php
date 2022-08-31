<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    abstract public function match(Zend_Controller_Request_Http $request);
}
