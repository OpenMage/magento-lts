<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert action abstract
 *
 * Instances of this class are used as actions in profile
 *
 * @package    Mage_Dataflow
 */
abstract class Mage_Dataflow_Model_Convert_Action_Abstract implements Mage_Dataflow_Model_Convert_Action_Interface
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
     * @var Mage_Dataflow_Model_Convert_Profile_Abstract
     */
    protected $_profile;

    protected $_actions = [];

    /**
     * Action's container
     *
     * @var Mage_Dataflow_Model_Convert_Container_Abstract
     */
    protected $_container;

    protected $_actionDefaultClass = 'Mage_Dataflow_Model_Convert_Action';

    /**
     * Get action parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return $this->_params[$key] ?? $default;
    }

    /**
     * Set action parameter
     *
     * @param string $key
     * @param mixed $value
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function setParam($key, $value = null)
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
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get profile instance the action belongs to
     *
     * @return Mage_Dataflow_Model_Convert_Profile_Abstract
     */
    public function getProfile()
    {
        return $this->_profile;
    }

    /**
     * Set profile instance the action belongs to
     *
     * @param Mage_Dataflow_Model_Convert_Profile_Abstract $profile
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function setProfile(Mage_Dataflow_Model_Convert_Profile_Interface $profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    public function addAction(?Mage_Dataflow_Model_Convert_Action_Interface $action = null)
    {
        if (is_null($action)) {
            $action = new $this->_actionDefaultClass();
        }
        $this->_actions[] = $action;
        $action->setProfile($this->getProfile());
        return $action;
    }

    /**
     * Set action's container
     *
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function setContainer(Mage_Dataflow_Model_Convert_Container_Interface $container)
    {
        $this->_container = $container;
        $this->_container->setProfile($this->getProfile());
        $this->_container->setAction($this);
        return $this;
    }

    /**
     * Get action's container
     *
     * @param string $name
     * @return Mage_Dataflow_Model_Convert_Container_Abstract
     */
    public function getContainer($name = null)
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

    public function importXml(Varien_Simplexml_Element $actionNode)
    {
        foreach ($actionNode->attributes() as $key => $value) {
            $this->setParam($key, (string) $value);
        }

        if ($actionNode['use']) {
            $container = $this->getProfile()->getContainer((string) $actionNode['use']);
        } else {
            $this->setParam('class', $this->getClassNameByType((string) $actionNode['type']));
            $container = $action->getContainer();
        }
        $this->setContainer($container);
        if ($this->getParam('name')) {
            $this->getProfile()->addContainer($this->getParam('name'), $container);
        }
        foreach ($actionNode->var as $varNode) {
            $container->setVar((string) $varNode['name'], (string) $varNode);
        }
        foreach ($actionNode->action as $actionSubnode) {
            $action = $this->addAction();
            $action->importXml($actionSubnode);
        }

        return $this;
    }

    /**
     * Run current action
     *
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function run(array $args = [])
    {
        if ($method = $this->getParam('method')) {
            if (!method_exists($this->getContainer(), $method)) {
                $this->getContainer()->addException(
                    'Unable to run action method: ' . $method,
                    Mage_Dataflow_Model_Convert_Exception::FATAL,
                );
            }

            $this->getContainer()->addException('Starting ' . $this->getContainer()::class . ' :: ' . $method);

            if ($this->getParam('from')) {
                $this->getContainer()->setData($this->getContainer($this->getParam('from'))->getData());
            }

            $this->getContainer()->$method($args);

            if ($this->getParam('to')) {
                $this->getContainer($this->getParam('to'))->setData($this->getContainer()->getData());
            }
        } else {
            $this->getContainer()->addException('No method specified', Mage_Dataflow_Model_Convert_Exception::FATAL);
        }
        return $this;
    }

    public function runActions(array $args = [])
    {
        if (empty($this->_actions)) {
            return $this;
        }
        foreach ($this->_actions as $action) {
            $action->run($args);
        }
        return $this;
    }
}
