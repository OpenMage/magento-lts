<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert profile
 *
 * @package    Varien_Convert
 */
abstract class Varien_Convert_Profile_Abstract
{
    protected $_actions;
    protected $_containers;
    protected $_exceptions = [];
    protected $_dryRun;

    protected $_actionDefaultClass = 'Varien_Convert_Action';
    protected $_containerCollectionDefaultClass = 'Varien_Convert_Container_Collection';

    public function addAction(?Varien_Convert_Action_Interface $action = null)
    {
        if (is_null($action)) {
            $action = new $this->_actionDefaultClass();
        }
        $this->_actions[] = $action;
        $action->setProfile($this);
        return $action;
    }

    public function setContainers(Varien_Convert_Container_Collection $containers)
    {
        $this->_containers = $containers;
        return $this;
    }

    public function getContainers()
    {
        if (!$this->_containers) {
            $this->_containers = new $this->_containerCollectionDefaultClass();
        }
        return $this->_containers;
    }

    public function getContainer($name = null)
    {
        if (is_null($name)) {
            $name = '_default';
        }
        return $this->getContainers()->getItem($name);
    }

    public function addContainer($name, Varien_Convert_Container_Interface $container)
    {
        $container = $this->getContainers()->addItem($name, $container);
        $container->setProfile($this);
        return $container;
    }

    public function getExceptions()
    {
        return $this->_exceptions;
    }

    public function getDryRun()
    {
        return $this->_dryRun;
    }

    public function setDryRun($flag)
    {
        $this->_dryRun = $flag;
        return $this;
    }

    public function addException(Varien_Convert_Exception $e)
    {
        $this->_exceptions[] = $e;
        return $this;
    }

    public function run()
    {
        if (!$this->_actions) {
            $e = new Varien_Convert_Exception('Could not find any actions for this profile');
            $e->setLevel(Varien_Convert_Exception::FATAL);
            $this->addException($e);
            return;
        }

        foreach ($this->_actions as $action) {
            $action->run();
        }
        return $this;
    }
}
