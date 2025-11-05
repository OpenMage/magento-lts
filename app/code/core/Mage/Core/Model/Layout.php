<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Layout model
 *
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
    protected $_blocks = [];

    /**
     * Cache of block callbacks to output during rendering
     *
     * @var array
     */
    protected $_output = [];

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
    protected $_helpers = [];

    /**
     * Flag to have blocks' output go directly to browser as opposed to return result
     *
     * @var bool
     */
    protected $_directOutput = false;

    protected $invalidActions
        = [
            // explicitly not using class constant here Mage_Page_Block_Html_Topmenu_Renderer::class
            // if the class does not exists it breaks.
            ['block' => 'Mage_Page_Block_Html_Topmenu_Renderer', 'method' => 'render'],
            ['block' => 'Mage_Core_Block_Template', 'method' => 'fetchview'],
        ];

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data = [])
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
     * Layout xml generation
     *
     * @return $this
     */
    public function generateXml()
    {
        $xml = $this->getUpdate()->asSimplexml();
        $removeInstructions = $xml->xpath('//remove');
        if (is_array($removeInstructions)) {
            foreach ($removeInstructions as $infoNode) {
                $attributes = $infoNode->attributes();
                $blockName = (string) $attributes->name;
                if ($blockName) {
                    $ignoreNodes = $xml->xpath("//block[@name='" . $blockName . "']");
                    if (!is_array($ignoreNodes)) {
                        continue;
                    }

                    $ignoreReferences = $xml->xpath("//reference[@name='" . $blockName . "']");
                    if (is_array($ignoreReferences)) {
                        $ignoreNodes = array_merge($ignoreNodes, $ignoreReferences);
                    }

                    foreach ($ignoreNodes as $block) {
                        if ($block->getAttribute('ignore') !== null) {
                            continue;
                        }

                        $acl = (string) $attributes->acl;
                        if ($acl && Mage::getSingleton('admin/session')->isAllowed($acl)) {
                            continue;
                        }

                        if (!isset($block->attributes()->ignore)) {
                            $block->addAttribute('ignore', '1');
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
     * @param null|Mage_Core_Model_Layout_Element|Varien_Simplexml_Element $parent
     */
    public function generateBlocks($parent = null)
    {
        if (empty($parent)) {
            $parent = $this->getNode();
        }

        foreach ($parent as $node) {
            $attributes = $node->attributes();
            if ((bool) $attributes->ignore) {
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
     * @param Mage_Core_Model_Layout_Element|Varien_Simplexml_Element $parent
     * @return $this
     */
    protected function _generateBlock($node, $parent)
    {
        if (!empty($node['class'])) {
            $className = (string) $node['class'];
        } else {
            $className = (string) $node['type'];
        }

        $blockName = (string) $node['name'];
        $profilerKey = 'BLOCK: ' . $blockName;
        Varien_Profiler::start($profilerKey);

        $block = $this->addBlock($className, $blockName);
        if (!$block) {
            return $this;
        }

        if (!empty($node['parent'])) {
            $parentBlock = $this->getBlock((string) $node['parent']);
        } else {
            $parentName = $parent->getBlockName();
            if (!empty($parentName)) {
                $parentBlock = $this->getBlock($parentName);
            }
        }

        if (!empty($parentBlock)) {
            $alias = isset($node['as']) ? (string) $node['as'] : '';
            if (isset($node['before'])) {
                $sibling = (string) $node['before'];
                if ($sibling === '-') {
                    $sibling = '';
                }

                $parentBlock->insert($block, $sibling, false, $alias);
            } elseif (isset($node['after'])) {
                $sibling = (string) $node['after'];
                if ($sibling === '-') {
                    $sibling = '';
                }

                $parentBlock->insert($block, $sibling, true, $alias);
            } else {
                $parentBlock->append($block, $alias);
            }
        }

        if (!empty($node['template'])) {
            $block->setTemplate((string) $node['template']);
        }

        if (!empty($node['output'])) {
            $method = (string) $node['output'];
            $this->addOutputBlock($blockName, $method);
        }

        Varien_Profiler::stop($profilerKey);

        return $this;
    }

    /**
     * @param Varien_Simplexml_Element $node
     * @param Mage_Core_Model_Layout_Element|Varien_Simplexml_Element $parent
     * @return $this
     */
    protected function _generateAction($node, $parent)
    {
        if (isset($node['ifconfig']) && ($configPath = (string) $node['ifconfig'])) {
            if (!Mage::getStoreConfigFlag($configPath)) {
                return $this;
            }
        }

        $method = (string) $node['method'];
        if (!empty($node['block'])) {
            $parentName = (string) $node['block'];
        } else {
            $parentName = $parent->getBlockName();
        }

        $profilerKey = 'BLOCK ACTION: ' . $parentName . ' -> ' . $method;
        Varien_Profiler::start($profilerKey);

        if (!empty($parentName)) {
            $block = $this->getBlock($parentName);
        }

        if (!empty($block)) {
            $args = (array) $node->children();
            unset($args['@attributes']);

            foreach ($args as $key => $arg) {
                if (($arg instanceof Mage_Core_Model_Layout_Element)) {
                    if (isset($arg['helper'])) {
                        $helperName = explode('/', (string) $arg['helper']);
                        $helperMethod = array_pop($helperName);
                        $helperName = implode('/', $helperName);
                        $arg = $arg->asArray();
                        unset($arg['@']);
                        $args[$key] = call_user_func_array([Mage::helper($helperName), $helperMethod], $arg);
                    } else {
                        /**
                         * if there is no helper we hope that this is assoc array
                         */
                        $arr = [];
                        /**
                         * @var string $subkey
                         * @var Mage_Core_Model_Layout_Element $value
                         */
                        foreach ($arg as $subkey => $value) {
                            $arr[(string) $subkey] = $value->asArray();
                        }

                        if (!empty($arr)) {
                            $args[$key] = $arr;
                        }
                    }
                }
            }

            if (isset($node['json'])) {
                $json = explode(' ', (string) $node['json']);
                foreach ($json as $arg) {
                    $args[$arg] = Mage::helper('core')->jsonDecode($args[$arg]);
                }
            }

            Mage::helper('core/security')->validateAgainstBlockMethodBlacklist($block, $method, $args);

            $this->_translateLayoutNode($node, $args);
            call_user_func_array([$block, $method], array_values($args));
        }

        Varien_Profiler::stop($profilerKey);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param string                   $method
     * @param string[]                 $args
     * @throws Mage_Core_Exception
     * @deprecated
     * @see Mage_Core_Helper_Security::validateAgainstBlockMethodBlacklist()
     */
    protected function validateAgainstBlacklist(Mage_Core_Block_Abstract $block, $method, array $args)
    {
        foreach ($this->invalidActions as $action) {
            if ($block instanceof $action['block'] && $action['method'] === strtolower($method)) {
                Mage::throwException(
                    sprintf('Action with combination block %s and method %s is forbidden.', $block::class, $method),
                );
            }
        }
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
            $moduleName = (isset($node['module'])) ? (string) $node['module'] : 'core';

            // Handle translations in arrays if needed
            $translatableArguments = explode(' ', (string) $node['translate']);
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
                while (is_array($argumentStack) && $argumentStack !== []) {
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
     * @param Mage_Core_Block_Abstract $block
     * @return $this
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
     * @return $this
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
     * @param     null|string $name
     * @return    false|Mage_Core_Block_Abstract
     */
    public function createBlock($type, $name = '', array $attributes = [])
    {
        try {
            $block = $this->_getBlockInstance($type, $attributes);
        } catch (Exception $exception) {
            Mage::logException($exception);
            return false;
        }

        if (empty($name) || $name[0] === '.') {
            $block->setIsAnonymous(true);
            if (!empty($name)) {
                $block->setAnonSuffix(substr($name, 1));
            }

            $name = 'ANONYMOUS_' . count($this->_blocks);
        } elseif (isset($this->_blocks[$name]) && Mage::getIsDeveloperMode()) {
            //Mage::throwException(Mage::helper('core')->__('Block with name "%s" already exists', $name));
        }

        $block->setType($type);
        $block->setNameInLayout($name);
        $block->addData($attributes);
        $block->setLayout($this);

        $this->_blocks[$name] = $block;
        Mage::dispatchEvent('core_layout_block_create_after', ['block' => $block]);
        return $this->_blocks[$name];
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param Mage_Core_Block_Abstract|string $block
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
     * @return Mage_Core_Block_Abstract
     */
    protected function _getBlockInstance($block, array $attributes = [])
    {
        if (is_string($block)) {
            if (str_contains($block, '/')) {
                if (!$block = Mage::getConfig()->getBlockClassName($block)) {
                    Mage::throwException(Mage::helper('core')->__('Invalid block type: %s', $block));
                }
            }

            if (class_exists($block, false) || mageFindClassFile($block)) {
                $block = new $block($attributes);
            }
        }

        if (!$block instanceof Mage_Core_Block_Abstract) {
            $block = is_object($block) ? $block::class : $block;
            Mage::throwException(Mage::helper('core')->__('Invalid block type: %s (not instance of Mage_Core_Block_Abstract)', $block));
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
     * @return false|Mage_Core_Block_Abstract
     */
    public function getBlock($name)
    {
        return $this->_blocks[$name] ?? false;
    }

    /**
     * Add a block to output
     *
     * @param string $blockName
     * @param string $method
     * @return $this
     */
    public function addOutputBlock($blockName, $method = 'toHtml')
    {
        //$this->_output[] = array($blockName, $method);
        $this->_output[$blockName] = [$blockName, $method];
        return $this;
    }

    /**
     * @param string $blockName
     * @return $this
     */
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
        foreach ($this->_output as $callback) {
            $out .= $this->getBlock($callback[0])->{$callback[1]}();
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
        if ($block instanceof Mage_Core_Block_Messages) {
            return $block;
        }

        return $this->createBlock('core/messages', 'messages');
    }

    /**
     * @param string $type
     * @return Mage_Core_Block_Abstract|object
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
     * @return  false|Mage_Core_Helper_Abstract
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
     * @return string
     */
    public static function findTranslationModuleName(Varien_Simplexml_Element $node)
    {
        $result = $node->getAttribute('module');
        if ($result) {
            return (string) $result;
        }

        /** @var Varien_Simplexml_Element $element */
        foreach (array_reverse($node->xpath('ancestor::*[@module]')) as $element) {
            $result = $element->getAttribute('module');
            if ($result) {
                return (string) $result;
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
