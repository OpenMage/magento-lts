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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert action abstract
 *
 * Instances of this class are used as actions in profile
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Varien_Convert_Action_Abstract implements Varien_Convert_Action_Interface
{
    /**
     * Action parameters
     *
     * Hold information about action container
     *
     * @var array
     */
    protected $_params;

    /**
     * Reference to profile this action belongs to
     *
     * @var Varien_Convert_Profile_Abstract
     */
    protected $_profile;

    /**
     * Action's container
     *
     * @var Varien_Convert_Container_Abstract
     */
    protected $_container;

    /**
     * Get action parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam($key, $default=null)
    {
        if (!isset($this->_params[$key])) {
            return $default;
        }
        return $this->_params[$key];
    }

    /**
     * Set action parameter
     *
     * @param string $key
     * @param mixed $value
     * @return Varien_Convert_Action_Abstract
     */
    public function setParam($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_params = $key;
        } else {
            $this->_params[$key] = $value;
        }
        return $this;
    }

    /**
     * Get all action parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set all action parameters
     *
     * @param array $params
     * @return Varien_Convert_Action_Abstract
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get profile instance the action belongs to
     *
     * @return Varien_Convert_Profile_Abstract
     */
    public function getProfile()
    {
        return $this->_profile;
    }

    /**
     * Set profile instance the action belongs to
     *
     * @param Varien_Convert_Profile_Abstract $profile
     * @return Varien_Convert_Action_Abstract
     */
    public function setProfile(Varien_Convert_Profile_Abstract $profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    /**
     * Set action's container
     *
     * @param Varien_Convert_Container_Abstract $container
     * @return Varien_Convert_Action_Abstract
     */
    public function setContainer(Varien_Convert_Container_Interface $container)
    {
        $this->_container = $container;
        $this->_container->setProfile($this->getProfile());
        return $this;
    }

    /**
     * Get action's container
     *
     * @param string $name
     * @return Varien_Convert_Container_Abstract
     */
    public function getContainer($name=null)
    {
        if (!is_null($name)) {
            return $this->getProfile()->getContainer($name);
        }

        if (!$this->_container) {
            $class = $this->getParam('class');
            $this->setContainer(new $class());
        }
        return $this->_container;
    }

    /**
     * Run current action
     *
     * @return Varien_Convert_Action_Abstract
     */
    public function run()
    {
        if ($method = $this->getParam('method')) {
            if (!is_callable(array($this->getContainer(), $method))) {
                $this->addException('Unable to run action method: '.$method, Varien_Convert_Exception::FATAL);
            }

            $this->getContainer()->addException('Starting '.get_class($this->getContainer()).' :: '.$method);

            if ($this->getParam('from')) {
                $this->getContainer()->setData($this->getContainer($this->getParam('from'))->getData());
            }

            $this->getContainer()->$method();

            if ($this->getParam('to')) {
                $this->getContainer($this->getParam('to'))->setData($this->getContainer()->getData());
            }
        } else {
            $this->addException('No method specified', Varien_Convert_Exception::FATAL);
        }
        return $this;
    }

}