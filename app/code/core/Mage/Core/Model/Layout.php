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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Layout model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Layout extends Varien_Simplexml_Config
{

    /**
     * Layout Update module
     *
     * @var Mage_Core_Model_Layout_Update
     */
    protected $_update;

    /**
     * Blocks registry
     *
     * @var array
     */
    protected $_blocks = array();

    /**
     * Cache of block callbacks to output during rendering
     *
     * @var array
     */
    protected $_output = array();

    /**
     * Layout area (f.e. admin, frontend)
     *
     * @var string
     */
    protected $_area;

    /**
     * Helper blocks cache for this layout
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * Flag to have blocks' output go directly to browser as oppose to return result
     *
     * @var boolean
     */
    protected $_directOutput = false;

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data=array())
    {
        $this->_elementClass = Mage::getConfig()->getModelClassName('core/layout_element');
        $this->setXml(simplexml_load_string('<layout/>', $this->_elementClass));
        $this->_update = Mage::getModel('core/layout_update');
        parent::__construct($data);
    }

    /**
     * Layout update instance
     *
     * @return Mage_Core_Model_Layout_Update
     */
    public function getUpdate()
    {
        return $this->_update;
    }

    /**
     * Set layout area
     *
     * @param   string $area
     * @return  Mage_Core_Model_Layout
     */
    public function setArea($area)
    {
        $this->_area = $area;
        return $this;
    }

    /**
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Declaring layout direct output flag
     *
     * @param   bool $flag
     * @return  Mage_Core_Model_Layout
     */
    public function setDirectOutput($flag)
    {
        $this->_directOutput = $flag;
        return $this;
    }

    /**
     * Retrieve derect output flag
     *
     * @return bool
     */
    public function getDirectOutput()
    {
        return $this->_directOutput;
    }

    /**
     * Loyout xml generation
     *
     * @return $this
     */
    public function generateXml()
    {
        $xml = $this->getUpdate()->asSimplexml();
        $removeInstructions = $xml->xpath("//remove");
        if (is_array($removeInstructions)) {
            foreach ($removeInstructions as $infoNode) {
                $attributes = $infoNode->attributes();
                $blockName = (string)$attributes->name;
                if ($blockName) {
                    $ignoreNodes = $xml->xpath("//block[@name='".$blockName."']");
                    if (!is_array($ignoreNodes)) {
                        continue;
                    }
                    $ignoreReferences = $xml->xpath("//reference[@name='".$blockName."']");
                    if (is_array($ignoreReferences)) {
                        $ignoreNodes = array_merge($ignoreNodes, $ignoreReferences);
                    }

                    foreach ($ignoreNodes as $block) {
                        if ($block->getAttribute('ignore') !== null) {
                            continue;
                        }
                        $acl = (string)$attributes->acl;
                        if ($acl && Mage::getSingleton('admin/session')->isAllowed($acl)) {
                            continue;
                        }
                        if (!isset($block->attributes()->ignore)) {
                            $block->addAttribute('ignore', true);
                        }
                    }
                }
            }
        }
        $this->setXml($xml);
        return $this;
    }

    /**
     * Create layout blocks hierarchy from layout xml configuration
     *
     * @param Mage_Core_Layout_Element|null $parent
     */
    public function generateBlocks($parent=null)
    {
        if (empty($parent)) {
            $parent = $this->getNode();
        }
        foreach ($parent as $node) {
            $attributes = $node->attributes();
            if ((bool)$attributes->ignore) {
                continue;
            }
            switch ($node->getName()) {
                case 'block':
                    $this->_generateBlock($node, $parent);
                    $this->generateBlocks($node);
                    break;

                case 'reference':
                    $this->generateBlocks($node);
                    break;

                case 'action':
                    $this->_generateAction($node, $parent);
                    break;
            }
        }
    }

    /**
     * Add block object to layout based on xml node data
     *
     * @param Varien_Simplexml_Element $node
     * @param Varien_Simplexml_Element $parent
     * @return $this
     */
    protected function _generateBlock($node, $parent)
    {
        if (!empty($node['class'])) {
            $className = (string)$node['class'];
        } else {
            $className = (string)$node['type'];
        }

        $blockName = (string)$node['name'];
        $_profilerKey = 'BLOCK: '.$blockName;
        Varien_Profiler::start($_profilerKey);

        $block = $this->addBlock($className, $blockName);
        if (!$block) {
            return $this;
        }

        if (!empty($node['parent'])) {
            $parentBlock = $this->getBlock((string)$node['parent']);
        } else {
            $parentName = $parent->getBlockName();
            if (!empty($parentName)) {
                $parentBlock = $this->getBlock($parentName);
            }
        }
        if (!empty($parentBlock)) {
            $alias = isset($node['as']) ? (string)$node['as'] : '';
            if (isset($node['before'])) {
                $sibling = (string)$node['before'];
                if ('-'===$sibling) {
                    $sibling = '';
                }
                $parentBlock->insert($block, $sibling, false, $alias);
            } elseif (isset($node['after'])) {
                $sibling = (string)$node['after'];
                if ('-'===$sibling) {
                    $sibling = '';
                }
                $parentBlock->insert($block, $sibling, true, $alias);
            } else {
                $parentBlock->append($block, $alias);
            }
        }
        if (!empty($node['template'])) {
            $block->setTemplate((string)$node['template']);
        }

        if (!empty($node['output'])) {
            $method = (string)$node['output'];
            $this->addOutputBlock($blockName, $method);
        }
        Varien_Profiler::stop($_profilerKey);

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Simplexml_Element $node
     * @param Varien_Simplexml_Element $parent
     * @return $this
     */
    protected function _generateAction($node, $parent)
    {
        if (isset($node['ifconfig']) && ($configPath = (string)$node['ifconfig'])) {
            if (!Mage::getStoreConfigFlag($configPath)) {
                return $this;
            }
        }

        $method = (string)$node['method'];
        if (!empty($node['block'])) {
            $parentName = (string)$node['block'];
        } else {
            $parentName = $parent->getBlockName();
        }

        $_profilerKey = 'BLOCK ACTION: '.$parentName.' -> '.$method;
        Varien_Profiler::start($_profilerKey);

        if (!empty($parentName)) {
            $block = $this->getBlock($parentName);
        }
        if (!empty($block)) {

            $args = (array)$node->children();
            unset($args['@attributes']);

            foreach ($args as $key => $arg) {
                if (($arg instanceof Mage_Core_Model_Layout_Element)) {
                    if (isset($arg['helper'])) {
                        $helperName = explode('/', (string)$arg['helper']);
                        $helperMethod = array_pop($helperName);
                        $helperName = implode('/', $helperName);
                        $arg = $arg->asArray();
                        unset($arg['@']);
                        $args[$key] = call_user_func_array(array(Mage::helper($helperName), $helperMethod), $arg);
                    } else {
                        /**
                         * if there is no helper we hope that this is assoc array
                         */
                        $arr = array();
                        foreach($arg as $subkey => $value) {
                            $arr[(string)$subkey] = $value->asArray();
                        }
                        if (!empty($arr)) {
                            $args[$key] = $arr;
                        }
                    }
                }
            }

            if (isset($node['json'])) {
                $json = explode(' ', (string)$node['json']);
                foreach ($json as $arg) {
                    $args[$arg] = Mage::helper('core')->jsonDecode($args[$arg]);
                }
            }

            $this->_translateLayoutNode($node, $args);
            call_user_func_array(array($block, $method), $args);
        }

        Varien_Profiler::stop($_profilerKey);

        return $this;
    }

    /**
     * Translate layout node
     *
     * @param Varien_Simplexml_Element $node
     * @param array $args
     **/
    protected function _translateLayoutNode($node, &$args)
    {
        if (isset($node['translate'])) {
            // Translate value by core module if module attribute was not set
            $moduleName = (isset($node['module'])) ? (string)$node['module'] : 'core';

            // Handle translations in arrays if needed
            $translatableArguments = explode(' ', (string)$node['translate']);
            foreach ($translatableArguments as $translatableArgumentName) {
                /*
                 * .(dot) character is used as a path separator in nodes hierarchy
                 * e.g. info.title means that Magento needs to translate value of <title> node
                 * that is a child of <info> node
                 */
                // @var $argumentHierarhy array - path to translatable item in $args array
                $argumentHierarchy = explode('.', $translatableArgumentName);
                $argumentStack = &$args;
                $canTranslate = true;
                while (is_array($argumentStack) && count($argumentStack) > 0) {
                    $argumentName = array_shift($argumentHierarchy);
                    if (isset($argumentStack[$argumentName])) {
                        /*
                         * Move to the next element in arguments hieracrhy
                         * in order to find target translatable argument
                         */
                        $argumentStack = &$argumentStack[$argumentName];
                    } else {
                        // Target argument cannot be found
                        $canTranslate = false;
                        break;
                    }
                }
                if ($canTranslate && is_string($argumentStack)) {
                    // $argumentStack is now a reference to target translatable argument so it can be translated
                    $argumentStack = Mage::helper($moduleName)->__($argumentStack);
                }
            }
        }
    }

    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param Mage_Core_Model_Layout $block
     */
    public function setBlock($name, $block)
    {
        $this->_blocks[$name] = $block;
        return $this;
    }

    /**
     * Remove block from registry
     *
     * @param string $name
     */
    public function unsetBlock($name)
    {
        $this->_blocks[$name] = null;
        unset($this->_blocks[$name]);
        return $this;
    }

    /**
     * Block Factory
     *
     * @param     string $type
     * @param     string $name
     * @param     array $attributes
     * @return    Mage_Core_Block_Abstract
     */
    public function createBlock($type, $name='', array $attributes = array())
    {
        try {
            $block = $this->_getBlockInstance($type, $attributes);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        if (empty($name) || '.'===$name[0]) {
            $block->setIsAnonymous(true);
            if (!empty($name)) {
                $block->setAnonSuffix(substr($name, 1));
            }
            $name = 'ANONYMOUS_'.sizeof($this->_blocks);
        } elseif (isset($this->_blocks[$name]) && Mage::getIsDeveloperMode()) {
            //Mage::throwException(Mage::helper('core')->__('Block with name "%s" already exists', $name));
        }

        $block->setType($type);
        $block->setNameInLayout($name);
        $block->addData($attributes);
        $block->setLayout($this);

        $this->_blocks[$name] = $block;
        Mage::dispatchEvent('core_layout_block_create_after', array('block'=>$block));
        return $this->_blocks[$name];
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param string|Mage_Core_Block_Abstract $blockClass
     * @param string $blockName
     * @return Mage_Core_Block_Abstract
     */
    public function addBlock($block, $blockName)
    {
        return $this->createBlock($block, $blockName);
    }

    /**
     * Create block object instance based on block type
     *
     * @param string $block
     * @param array $attributes
     * @return Mage_Core_Block_Abstract
     */
    protected function _getBlockInstance($block, array $attributes=array())
    {
        if (is_string($block)) {
            if (strpos($block, '/')!==false) {
                if (!$block = Mage::getConfig()->getBlockClassName($block)) {
                    Mage::throwException(Mage::helper('core')->__('Invalid block type: %s', $block));
                }
            }
            if (class_exists($block, false) || mageFindClassFile($block)) {
                $block = new $block($attributes);
            }
        }
        if (!$block instanceof Mage_Core_Block_Abstract) {
            Mage::throwException(Mage::helper('core')->__('Invalid block type: %s', $block));
        }
        return $block;
    }


    /**
     * Retrieve all blocks from registry as array
     *
     * @return array
     */
    public function getAllBlocks()
    {
        return $this->_blocks;
    }

    /**
     * Get block object by name
     *
     * @param string $name
     * @return Mage_Core_Block_Abstract
     */
    public function getBlock($name)
    {
        if (isset($this->_blocks[$name])) {
            return $this->_blocks[$name];
        } else {
            return false;
        }
    }

    /**
     * Add a block to output
     *
     * @param string $blockName
     * @param string $method
     */
    public function addOutputBlock($blockName, $method='toHtml')
    {
        //$this->_output[] = array($blockName, $method);
        $this->_output[$blockName] = array($blockName, $method);
        return $this;
    }

    public function removeOutputBlock($blockName)
    {
        unset($this->_output[$blockName]);
        return $this;
    }

    /**
     * Get all blocks marked for output
     *
     * @return string
     */
    public function getOutput()
    {
        $out = '';
        if (!empty($this->_output)) {
            foreach ($this->_output as $callback) {
                $out .= $this->getBlock($callback[0])->{$callback[1]}();
            }
        }

        return $out;
    }

    /**
     * Retrieve messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        $block = $this->getBlock('messages');
        if ($block) {
            return $block;
        }
        return $this->createBlock('core/messages', 'messages');
    }

    /**
     * Enter description here...
     *
     * @param string $type
     * @return Mage_Core_Helper_Abstract
     */
    public function getBlockSingleton($type)
    {
        if (!isset($this->_helpers[$type])) {
            $className = Mage::getConfig()->getBlockClassName($type);
            if (!$className) {
                Mage::throwException(Mage::helper('core')->__('Invalid block type: %s', $type));
            }

            $helper = new $className();
            if ($helper) {
                if ($helper instanceof Mage_Core_Block_Abstract) {
                    $helper->setLayout($this);
                }
                $this->_helpers[$type] = $helper;
            }
        }
        return $this->_helpers[$type];
    }

    /**
     * Retrieve helper object
     *
     * @param   string $name
     * @return  Mage_Core_Helper_Abstract
     */
    public function helper($name)
    {
        $helper = Mage::helper($name);
        if (!$helper) {
            return false;
        }
        return $helper->setLayout($this);
    }

    /**
     * Lookup module name for translation from current specified layout node
     *
     * Priorities:
     * 1) "module" attribute in the element
     * 2) "module" attribute in any ancestor element
     * 3) layout handle name - first 1 or 2 parts (namespace is determined automatically)
     *
     * @param Varien_Simplexml_Element $node
     * @return string
     */
    public static function findTranslationModuleName(Varien_Simplexml_Element $node)
    {
        $result = $node->getAttribute('module');
        if ($result) {
            return (string)$result;
        }
        foreach (array_reverse($node->xpath('ancestor::*[@module]')) as $element) {
            $result = $element->getAttribute('module');
            if ($result) {
                return (string)$result;
            }
        }
        foreach ($node->xpath('ancestor-or-self::*[last()-1]') as $handle) {
            $name = Mage::getConfig()->determineOmittedNamespace($handle->getName());
            if ($name) {
                return $name;
            }
        }
        return 'core';
    }
}
