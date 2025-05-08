<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert profile collection
 *
 * @package    Varien_Convert
 */
class Varien_Convert_Profile_Collection
{
    protected $_xml;
    protected $_containers;
    protected $_profiles = [];

    protected $_simplexmlDefaultClass = 'Varien_Simplexml_Element';
    protected $_profileDefaultClass = 'Varien_Convert_Profile';
    protected $_profileCollectionDefaultClass = 'Varien_Convert_Profile_Collection';
    protected $_containerDefaultClass = 'Varien_Convert_Container_Generic';
    protected $_containerCollectionDefaultClass = 'Varien_Convert_Container_Collection';

    public function getContainers()
    {
        if (!$this->_containers) {
            $this->_containers = new $this->_containerCollectionDefaultClass();
            $this->_containers->setDefaultClass($this->_containerDefaultClass);
        }
        return $this->_containers;
    }

    public function getContainer($name)
    {
        return $this->getContainers()->getItem($name);
    }

    public function addContainer($name, Varien_Convert_Container_Interface $container)
    {
        return $this->getContainers()->addItem($name, $container);
    }

    public function getProfiles()
    {
        return $this->_profiles;
    }

    public function getProfile($name)
    {
        if (!isset($this->_profiles[$name])) {
            $this->importProfileXml($name);
        }
        return $this->_profiles[$name];
    }

    public function addProfile($name, ?Varien_Convert_Profile_Abstract $profile = null)
    {
        if (is_null($profile)) {
            $profile = new $this->_profileDefaultClass();
        }
        $this->_profiles[$name] = $profile;
        return $profile;
    }

    public function run($profile)
    {
        $this->getProfile($profile)->run();
        return $this;
    }

    public function getClassNameByType($type)
    {
        return $type;
    }

    public function importXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml, $this->_simplexmlDefaultClass);
        }
        if (!$xml instanceof SimpleXMLElement) {
            return $this;
        }
        $this->_xml = $xml;

        foreach ($xml->container as $containerNode) {
            if (!$containerNode['name'] || !$containerNode['type']) {
                continue;
            }
            $class = $this->getClassNameByType((string) $containerNode['type']);
            $container = $this->addContainer((string) $containerNode['name'], new $class());
            foreach ($containerNode->var as $varNode) {
                $container->setVar((string) $varNode['name'], (string) $varNode);
            }
        }
        return $this;
    }

    public function importProfileXml($name)
    {
        if (!$this->_xml) {
            return $this;
        }
        $nodes = $this->_xml->xpath("//profile[@name='" . $name . "']");
        if (!$nodes) {
            return $this;
        }
        $profileNode = $nodes[0];

        $profile = $this->addProfile($name);
        $profile->setContainers($this->getContainers());
        foreach ($profileNode->action as $actionNode) {
            $action = $profile->addAction();
            foreach ($actionNode->attributes() as $key => $value) {
                $action->setParam($key, (string) $value);
            }

            if ($actionNode['use']) {
                $container = $profile->getContainer((string) $actionNode['use']);
            } else {
                $action->setParam('class', $this->getClassNameByType((string) $actionNode['type']));
                $container = $action->getContainer();
            }
            $action->setContainer($container);
            if ($action->getParam('name')) {
                $this->addContainer($action->getParam('name'), $container);
            }
            foreach ($actionNode->var as $varNode) {
                $container->setVar((string) $varNode['name'], (string) $varNode);
            }
        }

        return $this;
    }
}
