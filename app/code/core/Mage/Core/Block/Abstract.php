<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Base Content Block class
 *
 * For block generation you must define Data source class, data source class method,
 * parameters array and block template
 *
 * @package    Mage_Core
 *
 * @method $this setAdditionalHtml(string $value)
 * @method $this setBlockParams(array $value)
 * @method $this setCacheLifetime(int|false $value)
 * @method $this setCacheKey(string $value)
 * @method $this setCacheTags(array $value)
 * @method $this setClass(string $value)
 * @method $this setDisabled(bool $value)
 * @method $this setLabel(string $value)
 * @method $this setOnclick(string $value)
 * @method string getPosition()
 * @method $this setTemplate(string $value)
 * @method $this setType(string $value)
 * @method bool hasWrapperMustBeVisible()
 */
abstract class Mage_Core_Block_Abstract extends Varien_Object
{
    /**
     * Prefix for cache key
     */
    public const CACHE_KEY_PREFIX = 'BLOCK_';
    /**
     * Cache group Tag
     */
    public const CACHE_GROUP = 'block_html';

    /**
     * Cache tags data key
     */
    public const CACHE_TAGS_DATA_KEY = 'cache_tags';

    /**
     * Block name in layout
     *
     * @var string
     */
    protected $_nameInLayout;

    /**
     * Parent layout of the block
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Parent block
     *
     * @var Mage_Core_Block_Abstract
     */
    protected $_parent;

    /**
     * Short alias of this block that was referred from parent
     *
     * @var string
     */
    protected $_alias;

    /**
     * Suffix for name of anonymous block
     *
     * @var string
     */
    protected $_anonSuffix;

    /**
     * Contains references to child block objects
     *
     * @var Mage_Core_Block_Abstract[]
     */
    protected $_children = [];

    /**
     * Sorted children list
     *
     * @var array
     */
    protected $_sortedChildren = [];

    /**
     * Children blocks HTML cache array
     *
     * @var array
     */
    protected $_childrenHtmlCache = [];

    /**
     * Arbitrary groups of child blocks
     *
     * @var array
     */
    protected $_childGroups = [];

    /**
     * Request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Messages block instance
     *
     * @var Mage_Core_Block_Messages|null
     */
    protected $_messagesBlock = null;

    /**
     * Whether this block was not explicitly named
     *
     * @var bool
     */
    protected $_isAnonymous = false;

    /**
     * Parent block
     *
     * @var Mage_Core_Block_Abstract
     */
    protected $_parentBlock;

    /**
     * Block html frame open tag
     * @var string
     */
    protected $_frameOpenTag;

    /**
     * Block html frame close tag
     * @var string
     */
    protected $_frameCloseTag;

    /**
     * Url object
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_urlModel;

    /**
     * @var Varien_Object
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private static $_transportObject;

    /**
     * Array of block sort priority instructions
     *
     * @var array
     */
    protected $_sortInstructions = [];

    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory|null
     */
    protected $_factory;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App|null
     */
    protected $_app;

    /**
     * Initialize factory instance
     */
    public function __construct(array $args = [])
    {
        if (!empty($args['core_factory']) && ($args['core_factory'] instanceof Mage_Core_Model_Factory)) {
            $this->_factory = $args['core_factory'];
        }
        if (!empty($args['app']) && ($args['app'] instanceof Mage_Core_Model_App)) {
            $this->_app = $args['app'];
        }
        parent::__construct($args);
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * Please override this one instead of overriding real __construct constructor
     *
     */
    protected function _construct()
    {
        /**
         * Please override this one instead of overriding real __construct constructor
         */
    }

    /**
     * Retrieve factory instance
     *
     * @return Mage_Core_Model_Factory
     */
    protected function _getFactory()
    {
        return is_null($this->_factory) ? Mage::getSingleton('core/factory') : $this->_factory;
    }

    /**
     * Retrieve application instance
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return is_null($this->_app) ? Mage::app() : $this->_app;
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     * @throws Exception
     */
    public function getRequest()
    {
        $controller = $this->_getApp()->getFrontController();
        if ($controller) {
            $this->_request = $controller->getRequest();
        } else {
            throw new Exception(Mage::helper('core')->__("Can't retrieve request object"));
        }
        return $this->_request;
    }

    /**
     * Retrieve parent block
     *
     * @return $this
     */
    public function getParentBlock()
    {
        return $this->_parentBlock;
    }

    /**
     * Set parent block
     *
     * @return  $this
     */
    public function setParentBlock(Mage_Core_Block_Abstract $block)
    {
        $this->_parentBlock = $block;
        return $this;
    }

    /**
     * Retrieve current action object
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function getAction()
    {
        return $this->_getApp()->getFrontController()->getAction();
    }

    /**
     * Set layout object
     *
     * @return  $this
     */
    public function setLayout(Mage_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
        Mage::dispatchEvent('core_block_abstract_prepare_layout_before', ['block' => $this]);
        $this->_prepareLayout();
        Mage::dispatchEvent('core_block_abstract_prepare_layout_after', ['block' => $this]);
        return $this;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Retrieve layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Check if block is using auto generated (Anonymous) name
     * @return bool
     */
    public function getIsAnonymous()
    {
        return $this->_isAnonymous;
    }

    /**
     * Set the anonymous flag
     *
     * @param  bool $flag
     * @return $this
     */
    public function setIsAnonymous($flag)
    {
        $this->_isAnonymous = (bool) $flag;
        return $this;
    }

    /**
     * Returns anonymous block suffix
     *
     * @return string
     */
    public function getAnonSuffix()
    {
        return $this->_anonSuffix;
    }

    /**
     * Set anonymous suffix for current block
     *
     * @param string $suffix
     * @return $this
     */
    public function setAnonSuffix($suffix)
    {
        $this->_anonSuffix = $suffix;
        return $this;
    }

    /**
     * Returns block alias
     *
     * @return string
     */
    public function getBlockAlias()
    {
        return $this->_alias;
    }

    /**
     * Set block alias
     *
     * @param string $alias
     * @return $this
     */
    public function setBlockAlias($alias)
    {
        $this->_alias = $alias;
        return $this;
    }

    /**
     * Set block's name in layout and unsets previous link if such exists.
     *
     * @param string $name
     * @return $this
     */
    public function setNameInLayout($name)
    {
        if (!empty($this->_nameInLayout) && $this->getLayout()) {
            $this->getLayout()->unsetBlock($this->_nameInLayout)
                ->setBlock($name, $this);
        }
        $this->_nameInLayout = $name;
        return $this;
    }

    /**
     * Retrieve sorted list of children.
     *
     * @return array
     */
    public function getSortedChildren()
    {
        $this->sortChildren();
        return $this->_sortedChildren;
    }

    /**
     * Set block attribute value
     *
     * Wrapper for method "setData"
     *
     * @param   array|string $name
     * @param   mixed $value
     * @return  $this
     */
    public function setAttribute($name, $value = null)
    {
        return $this->setData($name, $value);
    }

    /**
     * Set child block
     *
     * @param   string $alias
     * @param   Mage_Core_Block_Abstract $block
     * @return  $this
     */
    public function setChild($alias, $block)
    {
        if (is_string($block)) {
            $block = $this->getLayout()->getBlock($block);
        }
        if (!$block) {
            return $this;
        }

        if ($block->getIsAnonymous()) {
            $suffix = $block->getAnonSuffix();
            if (empty($suffix)) {
                $suffix = 'child' . count($this->_children);
            }
            $blockName = $this->getNameInLayout() . '.' . $suffix;

            if ($this->getLayout()) {
                $this->getLayout()->unsetBlock($block->getNameInLayout())
                    ->setBlock($blockName, $block);
            }

            $block->setNameInLayout($blockName);
            $block->setIsAnonymous(false);

            if (empty($alias)) {
                $alias = $blockName;
            }
        }

        $block->setParentBlock($this);
        $block->setBlockAlias($alias);
        $this->_children[$alias] = $block;
        return $this;
    }

    /**
     * Unset child block
     *
     * @param  string $alias
     * @return $this
     */
    public function unsetChild($alias)
    {
        if (isset($this->_children[$alias])) {
            $block = $this->_children[$alias];
            $name = $block->getNameInLayout();
            unset($this->_children[$alias]);
            $key = array_search($name, $this->_sortedChildren);
            if ($key !== false) {
                array_splice($this->_sortedChildren, $key, 1);
            }
        }

        return $this;
    }

    /**
     * Call a child and unset it, if callback matched result
     *
     * $params will pass to child callback
     * $params may be array, if called from layout with elements with same name, for example:
     * ...<foo>value_1</foo><foo>value_2</foo><foo>value_3</foo>
     *
     * Or, if called like this:
     * ...<foo>value_1</foo><bar>value_2</bar><baz>value_3</baz>
     * - then it will be $params1, $params2, $params3
     *
     * It is no difference anyway, because they will be transformed in appropriate way.
     *
     * @param string $alias
     * @param string $callback
     * @param mixed $result
     * @param array $params
     * @return $this
     */
    public function unsetCallChild($alias, $callback, $result, $params)
    {
        $args = func_get_args();
        $child = $this->getChild($alias);
        if ($child) {
            $alias = array_shift($args);
            $callback = array_shift($args);
            $result = (string) array_shift($args);
            if (!is_array($params)) {
                $params = $args;
            }

            Mage::helper('core/security')->validateAgainstBlockMethodBlacklist($child, $callback, $params);
            if ($result == call_user_func_array([&$child, $callback], $params)) {
                $this->unsetChild($alias);
            }
        }
        return $this;
    }

    /**
     * Unset all children blocks
     *
     * @return $this
     */
    public function unsetChildren()
    {
        $this->_children = [];
        $this->_sortedChildren = [];
        return $this;
    }

    /**
     * Retrieve child block by name
     *
     * @param  string $name
     * @return mixed
     */
    public function getChild($name = '')
    {
        if ($name === '') {
            return $this->_children;
        } elseif (isset($this->_children[$name])) {
            return $this->_children[$name];
        }
        return false;
    }

    /**
     * Retrieve child block HTML
     *
     * @param   string $name
     * @param   bool $useCache
     * @param   bool $sorted
     * @return  string
     */
    public function getChildHtml($name = '', $useCache = true, $sorted = false)
    {
        if ($name === '') {
            if ($sorted) {
                $children = [];
                foreach ($this->getSortedChildren() as $childName) {
                    $children[$childName] = $this->getLayout()->getBlock($childName);
                }
            } else {
                $children = $this->getChild();
            }
            $out = '';
            foreach ($children as $child) {
                $out .= $this->_getChildHtml($child->getBlockAlias(), $useCache);
            }
            return $out;
        } else {
            return $this->_getChildHtml($name, $useCache);
        }
    }

    /**
     * @param string $name          Parent block name
     * @param string $childName     OPTIONAL Child block name
     * @param bool $useCache        OPTIONAL Use cache flag
     * @param bool $sorted          OPTIONAL @see getChildHtml()
     * @return string
     */
    public function getChildChildHtml($name, $childName = '', $useCache = true, $sorted = false)
    {
        if (empty($name)) {
            return '';
        }
        $child = $this->getChild($name);
        if (!$child) {
            return '';
        }
        return $child->getChildHtml($childName, $useCache, $sorted);
    }

    /**
     * Obtain sorted child blocks
     *
     * @return Mage_Core_Block_Abstract[]
     */
    public function getSortedChildBlocks()
    {
        $children = [];
        foreach ($this->getSortedChildren() as $childName) {
            $children[$childName] = $this->getLayout()->getBlock($childName);
        }
        return $children;
    }

    /**
     * Retrieve child block HTML
     *
     * @param   string $name
     * @param   bool $useCache
     * @return  string
     */
    protected function _getChildHtml($name, $useCache = true)
    {
        if ($useCache && isset($this->_childrenHtmlCache[$name])) {
            return $this->_childrenHtmlCache[$name];
        }

        $child = $this->getChild($name);

        if (!$child) {
            $html = '';
        } else {
            $this->_beforeChildToHtml($name, $child);
            $html = $child->toHtml();
        }

        $this->_childrenHtmlCache[$name] = $html;
        return $html;
    }

    /**
     * Prepare child block before generate html
     *
     * @param   string $name
     * @param   Mage_Core_Block_Abstract $child
     */
    protected function _beforeChildToHtml($name, $child) {}

    /**
     * Retrieve block html
     *
     * @param   string $name
     * @return  string
     */
    public function getBlockHtml($name)
    {
        if (!($layout = $this->getLayout()) && !($layout = $this->getAction()->getLayout())) {
            return '';
        }
        if (!($block = $layout->getBlock($name))) {
            return '';
        }
        return $block->toHtml();
    }

    /**
     * Insert child block
     *
     * @param   Mage_Core_Block_Abstract|string $block
     * @param   string $siblingName
     * @param   bool $after
     * @param   string $alias
     * @return  object $this
     */
    public function insert($block, $siblingName = '', $after = false, $alias = '')
    {
        if (is_string($block)) {
            $block = $this->getLayout()->getBlock($block);
        }
        if (!$block) {
            /*
             * if we don't have block - don't throw exception because
             * block can simply removed using layout method remove
             */
            //Mage::throwException(Mage::helper('core')
            // ->__('Invalid block name to set child %s: %s', $alias, $block));
            return $this;
        }
        if ($block->getIsAnonymous()) {
            $this->setChild('', $block);
            $name = $block->getNameInLayout();
        } elseif ($alias != '') {
            $this->setChild($alias, $block);
            $name = $block->getNameInLayout();
        } else {
            $name = $block->getNameInLayout();
            $this->setChild($name, $block);
        }

        $existingKey = array_search($name, $this->_sortedChildren);
        if ($existingKey !== false) {
            array_splice($this->_sortedChildren, $existingKey, 1);
        }

        if ($siblingName === '') {
            if ($after) {
                $this->_sortedChildren[] = $name;
            } else {
                array_unshift($this->_sortedChildren, $name);
            }
        } else {
            $key = array_search($siblingName, $this->_sortedChildren);
            if ($key !== false) {
                if ($after) {
                    $key++;
                }
                array_splice($this->_sortedChildren, $key, 0, $name);
            } elseif ($after) {
                $this->_sortedChildren[] = $name;
            } else {
                array_unshift($this->_sortedChildren, $name);
            }

            $this->_sortInstructions[$name] = [$siblingName, (bool) $after, $key !== false];
        }

        return $this;
    }

    /**
     * Sort block's children
     *
     * @param bool $force force re-sort all children
     * @return $this
     */
    public function sortChildren($force = false)
    {
        foreach ($this->_sortInstructions as $name => $list) {
            [$siblingName, $after, $exists] = $list;
            if ($exists && !$force) {
                continue;
            }
            $this->_sortInstructions[$name][2] = true;

            $index = array_search($name, $this->_sortedChildren);
            $siblingKey = array_search($siblingName, $this->_sortedChildren);

            if ($index === false || $siblingKey === false) {
                continue;
            }

            if ($after) {
                // insert after block
                if ($index == $siblingKey + 1) {
                    continue;
                }
                // remove sibling from array
                array_splice($this->_sortedChildren, $index, 1, []);
                // insert sibling after
                array_splice($this->_sortedChildren, $siblingKey + 1, 0, [$name]);
            } else {
                // insert before block
                if ($index == $siblingKey - 1) {
                    continue;
                }
                // remove sibling from array
                array_splice($this->_sortedChildren, $index, 1, []);
                // insert sibling after
                array_splice($this->_sortedChildren, $siblingKey, 0, [$name]);
            }
        }

        return $this;
    }

    /**
     * Append child block
     *
     * @param   Mage_Core_Block_Abstract|string $block
     * @param   string $alias
     * @return  $this
     */
    public function append($block, $alias = '')
    {
        $this->insert($block, '', true, $alias);
        return $this;
    }

    /**
     * Make sure specified block will be registered in the specified child groups
     *
     * @param string $groupName
     */
    public function addToChildGroup($groupName, Mage_Core_Block_Abstract $child)
    {
        if (!isset($this->_childGroups[$groupName])) {
            $this->_childGroups[$groupName] = [];
        }
        if (!in_array($child->getBlockAlias(), $this->_childGroups[$groupName])) {
            $this->_childGroups[$groupName][] = $child->getBlockAlias();
        }
    }

    /**
     * Add self to the specified group of parent block
     *
     * @param string $groupName
     * @return $this
     */
    public function addToParentGroup($groupName)
    {
        $this->getParentBlock()->addToChildGroup($groupName, $this);
        return $this;
    }

    /**
     * Get a group of child blocks
     *
     * Returns an array of <alias> => <block>
     * or an array of <alias> => <callback_result>
     * The callback currently supports only $this methods and passes the alias as parameter
     *
     * @param string $groupName
     * @param string $callback
     * @param bool $skipEmptyResults
     * @return array
     */
    public function getChildGroup($groupName, $callback = null, $skipEmptyResults = true)
    {
        $result = [];
        if (!isset($this->_childGroups[$groupName])) {
            return $result;
        }
        foreach ($this->getSortedChildBlocks() as $block) {
            $alias = $block->getBlockAlias();
            if (in_array($alias, $this->_childGroups[$groupName])) {
                if ($callback) {
                    Mage::helper('core/security')->validateAgainstBlockMethodBlacklist($this, $callback, [$alias]);
                    $row = $this->$callback($alias);
                    if (!$skipEmptyResults || $row) {
                        $result[$alias] = $row;
                    }
                } else {
                    $result[$alias] = $block;
                }
            }
        }
        return $result;
    }

    /**
     * Get a value from child block by specified key
     *
     * @param string $alias
     * @param string $key
     * @return mixed|void
     */
    public function getChildData($alias, $key = '')
    {
        $child = $this->getChild($alias);
        if ($child) {
            return $child->getData($key);
        }
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        return $this;
    }

    /**
     * Specify block output frame tags
     *
     * @param string $openTag
     * @param string $closeTag
     * @return $this
     */
    public function setFrameTags($openTag, $closeTag = null)
    {
        $this->_frameOpenTag = $openTag;
        if ($closeTag) {
            $this->_frameCloseTag = $closeTag;
        } else {
            $this->_frameCloseTag = '/' . $openTag;
        }
        return $this;
    }

    /**
     * Produce and return block's html output
     *
     * It is a final method, but you can override _toHtml() method in descendants if needed.
     *
     * @return string
     */
    final public function toHtml()
    {
        Mage::dispatchEvent('core_block_abstract_to_html_before', ['block' => $this]);
        if (Mage::getStoreConfig('advanced/modules_disable_output/' . $this->getModuleName())) {
            return '';
        }
        $html = $this->_loadCache();
        if ($html === false) {
            $translate = Mage::getSingleton('core/translate');
            /** @var Mage_Core_Model_Translate $translate */
            if ($this->hasData('translate_inline')) {
                $translate->setTranslateInline($this->getData('translate_inline'));
            }

            $this->_beforeToHtml();
            $html = $this->_toHtml();
            $this->_saveCache($html);

            if ($this->hasData('translate_inline')) {
                $translate->setTranslateInline(true);
            }
        }
        $html = $this->_afterToHtml($html);

        /**
         * Check framing options
         */
        if ($this->_frameOpenTag) {
            $html = '<' . $this->_frameOpenTag . '>' . $html . '<' . $this->_frameCloseTag . '>';
        }

        /**
         * Use single transport object instance for all blocks
         */
        if (self::$_transportObject === null) {
            self::$_transportObject = new Varien_Object();
        }
        self::$_transportObject->setHtml($html);
        Mage::dispatchEvent(
            'core_block_abstract_to_html_after',
            ['block' => $this, 'transport' => self::$_transportObject],
        );

        return self::$_transportObject->getHtml();
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        return $html;
    }

    /**
     * Override this method in descendants to produce html
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Returns url model class name
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }

    /**
     * Create and return url object
     *
     * @return Mage_Core_Model_Url
     */
    protected function _getUrlModel()
    {
        /** @var Mage_Core_Model_Url $model */
        $model = Mage::getModel($this->_getUrlModelClass());
        return $model;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_getUrlModel()->getUrl($route, $params);
    }

    /**
     * Generate security url by route and parameters (add form key if "Add Secret Key to URLs" disabled)
     *
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    public function getUrlSecure($route = '', $params = [])
    {
        if (!Mage::helper('adminhtml')->isEnabledSecurityKeyUrl()) {
            $params[Mage_Core_Model_Url::FORM_KEY] = $this->getFormKey();
        }
        return $this->getUrl($route, $params);
    }

    /**
     * Generate base64-encoded url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrlBase64($route = '', $params = [])
    {
        return Mage::helper('core')->urlEncode($this->getUrl($route, $params));
    }

    /**
     * Generate url-encoded url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrlEncoded($route = '', $params = [])
    {
        return Mage::helper('core')->urlEncode($this->getUrl($route, $params));
    }

    /**
     * Retrieve url of skins file
     *
     * @param   string $file path to file in skin
     * @return  string
     */
    public function getSkinUrl($file = null, array $params = [])
    {
        return Mage::getDesign()->getSkinUrl($file, $params);
    }

    /**
     * Retrieve messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        if (is_null($this->_messagesBlock)) {
            return $this->getLayout()->getMessagesBlock();
        }
        return $this->_messagesBlock;
    }

    /**
     * Set messages block
     *
     * @return  $this
     */
    public function setMessagesBlock(Mage_Core_Block_Messages $block)
    {
        $this->_messagesBlock = $block;
        return $this;
    }

    /**
     * Return block helper
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getHelper($type)
    {
        return $this->getLayout()->getBlockSingleton($type);
    }

    /**
     * Returns helper object
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    public function helper($name)
    {
        if ($this->getLayout()) {
            return $this->getLayout()->helper($name);
        }
        return Mage::helper($name);
    }

    /**
     * Retrieve formatting date
     *
     * @param string|int|Zend_Date|null $date
     * @param string $format
     * @param bool $showTime
     * @return string
     */
    public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');
        return $helper->formatDate($date, $format, $showTime);
    }

    /**
     * Retrieve formatting timezone date
     *
     * @param string|int|Zend_Date|null $date
     */
    public function formatTimezoneDate(
        $date = null,
        string $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
        bool $showTime = false,
        bool $useTimezone = true
    ): string {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');
        return $helper->formatTimezoneDate($date, $format, $showTime, $useTimezone);
    }

    /**
     * Retrieve formatting time
     *
     * @param   string $time
     * @param   string $format
     * @param   bool $showDate
     * @return  string
     */
    public function formatTime($time = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showDate = false)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');
        return $helper->formatTime($time, $format, $showDate);
    }

    /**
     * Retrieve module name of block
     *
     * @return string
     */
    public function getModuleName()
    {
        $module = $this->getData('module_name');
        if (is_null($module)) {
            $class = static::class;
            $module = substr($class, 0, strpos($class, '_Block'));
            $this->setData('module_name', $module);
        }
        return $module;
    }

    /**
     * Translate block sentence
     *
     * @return string
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->getModuleName());
        array_unshift($args, $expr);
        return $this->_getApp()->getTranslator()->translate($args);
    }

    /**
     * @param string|array $data
     * @param array $allowedTags
     * @return string
     * @see self::escapeHtml()
     * @deprecated after 1.4.0.0-rc1
     */
    public function htmlEscape($data, $allowedTags = null)
    {
        return $this->escapeHtml($data, $allowedTags);
    }

    /**
     * Escape html entities
     *
     * @param string|string[] $data
     * @param array|null $allowedTags
     * @return null|string|string[]
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->helper('core')->escapeHtml($data, $allowedTags);
    }

    /**
     * Wrapper for escapeHtml() function with keeping original value
     *
     * @param string[]|null $allowedTags
     *
     * @see Mage_Core_Model_Security_HtmlEscapedString::getUnescapedValue()
     */
    public function escapeHtmlAsObject(string $data, ?array $allowedTags = null): Mage_Core_Model_Security_HtmlEscapedString
    {
        return new Mage_Core_Model_Security_HtmlEscapedString($data, $allowedTags);
    }

    /**
     * Wrapper for escapeHtml() function with keeping original value
     *
     * @param string[] $data
     * @param string[]|null $allowedTags
     * @return Mage_Core_Model_Security_HtmlEscapedString[]
     *
     *  @see Mage_Core_Model_Security_HtmlEscapedString::getUnescapedValue()
     */
    public function escapeHtmlArrayAsObject(array $data, ?array $allowedTags = null): array
    {
        $result = [];
        foreach ($data as $key => $string) {
            $result[$key] = $this->escapeHtmlAsObject($string, $allowedTags);
        }

        return $result;
    }

    /**
     * Wrapper for standard strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string $allowableTags
     * @param bool $allowHtmlEntities
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $allowHtmlEntities = false)
    {
        return $this->helper('core')->stripTags($data, $allowableTags, $allowHtmlEntities);
    }

    /**
     * @param string $data
     * @return string
     * @deprecated after 1.4.0.0-rc1
     * @see self::escapeUrl()
     */
    public function urlEscape($data)
    {
        return $this->escapeUrl($data);
    }

    /**
     * Escape html entities in url
     *
     * @param string $data
     * @return string
     */
    public function escapeUrl($data)
    {
        return $this->helper('core')->escapeUrl($data);
    }

    /**
     * Escape quotes inside html attributes
     * Use $addSlashes = false for escaping js that inside html attribute (onClick, onSubmit etc)
     *
     * @param  string $data
     * @param  bool $addSlashes
     * @return string
     */
    public function quoteEscape($data, $addSlashes = false)
    {
        return $this->helper('core')->quoteEscape($data, $addSlashes);
    }

    /**
     * Escape quotes in java scripts
     *
     * @param mixed $data
     * @param string $quote
     * @return mixed
     */
    public function jsQuoteEscape($data, $quote = '\'')
    {
        return $this->helper('core')->jsQuoteEscape($data, $quote);
    }

    /**
     * Alias for getName method.
     *
     * @return string
     */
    public function getNameInLayout()
    {
        return $this->_nameInLayout;
    }

    /**
     * Get children blocks count
     * @return int
     */
    public function countChildren()
    {
        return count($this->_children);
    }

    /**
     * Prepare url for save to cache
     *
     * @return $this
     */
    protected function _beforeCacheUrl()
    {
        if ($this->_getApp()->useCache(self::CACHE_GROUP)) {
            $this->_getApp()->setUseSessionVar(true);
        }
        return $this;
    }

    /**
     * Replace URLs from cache
     *
     * @param string $html
     * @return string
     */
    protected function _afterCacheUrl($html)
    {
        if ($this->_getApp()->useCache(self::CACHE_GROUP)) {
            $this->_getApp()->setUseSessionVar(false);
            Varien_Profiler::start('CACHE_URL');
            /** @var Mage_Core_Model_Url $model */
            $model = Mage::getSingleton($this->_getUrlModelClass());
            $html = $model->sessionUrlVar($html);
            Varien_Profiler::stop('CACHE_URL');
        }
        return $html;
    }

    /**
     * Get cache key informative items
     * Provide string array key to share specific info item with FPC placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            $this->getNameInLayout(),
        ];
    }

    /**
     * Get Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        if ($this->hasData('cache_key')) {
            $cacheKey = $this->getData('cache_key');
            if (!str_starts_with($cacheKey, self::CACHE_KEY_PREFIX)) {
                $cacheKey = self::CACHE_KEY_PREFIX . $cacheKey;
                $this->setData('cache_key', $cacheKey);
            }

            return $cacheKey;
        }
        /**
         * don't prevent recalculation by saving generated cache key
         * because of ability to render single block instance with different data
         */
        $key = $this->getCacheKeyInfo();
        //ksort($key);  // ignore order
        $key = array_values($key); // ignore array keys
        $key = implode('|', $key);
        return sha1($key);
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        $tagsCache = $this->_getApp()->loadCache($this->_getTagsCacheKey());
        if ($tagsCache) {
            $tags = json_decode($tagsCache);
        }
        if (!isset($tags) || !is_array($tags) || empty($tags)) {
            $tags = !$this->hasData(self::CACHE_TAGS_DATA_KEY) ? [] : $this->getData(self::CACHE_TAGS_DATA_KEY);
            if (!in_array(self::CACHE_GROUP, $tags)) {
                $tags[] = self::CACHE_GROUP;
            }
        }
        return array_unique($tags);
    }

    /**
     * Add tag to block
     *
     * @param string|array $tag
     * @return $this
     */
    public function addCacheTag($tag)
    {
        $tag = is_array($tag) ? $tag : [$tag];
        $tags = !$this->hasData(self::CACHE_TAGS_DATA_KEY) ?
            $tag : array_merge($this->getData(self::CACHE_TAGS_DATA_KEY), $tag);
        $this->setData(self::CACHE_TAGS_DATA_KEY, $tags);
        return $this;
    }

    /**
     * Add tags from specified model to current block
     *
     * @return $this
     */
    public function addModelTags(Mage_Core_Model_Abstract $model)
    {
        $cacheTags = $model->getCacheIdTags();
        if ($cacheTags !== false) {
            $this->addCacheTag($cacheTags);
        }
        return $this;
    }

    /**
     * Get block cache lifetime
     *
     * @return int|null
     */
    public function getCacheLifetime()
    {
        if (!$this->hasData('cache_lifetime')) {
            return null;
        }
        return $this->getData('cache_lifetime');
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Load block html from cache storage
     *
     * @return string | false
     */
    protected function _loadCache()
    {
        if (is_null($this->getCacheLifetime()) || !$this->_getApp()->useCache(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        $cacheData = $this->_getApp()->loadCache($cacheKey);
        if ($cacheData) {
            $cacheData = str_replace(
                $this->_getSidPlaceholder($cacheKey),
                $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId(),
                $cacheData,
            );
        }
        return $cacheData;
    }

    /**
     * Save block content to cache storage
     *
     * @param string $data
     * @return $this|false
     */
    protected function _saveCache($data)
    {
        if (is_null($this->getCacheLifetime()) || !$this->_getApp()->useCache(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        $data = str_replace(
            $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId(),
            $this->_getSidPlaceholder($cacheKey),
            $data,
        );

        $tags = $this->getCacheTags();

        $this->_getApp()->saveCache($data, $cacheKey, $tags, $this->getCacheLifetime());
        $this->_getApp()->saveCache(
            json_encode($tags),
            $this->_getTagsCacheKey($cacheKey),
            $tags,
            $this->getCacheLifetime(),
        );
        return $this;
    }

    /**
     * Get cache key for tags
     *
     * @param string $cacheKey
     * @return string
     */
    protected function _getTagsCacheKey($cacheKey = null)
    {
        $cacheKey = !empty($cacheKey) ? $cacheKey : $this->getCacheKey();
        return md5($cacheKey . '_tags');
    }

    /**
     * Get SID placeholder for cache
     *
     * @param null|string $cacheKey
     * @return string
     */
    protected function _getSidPlaceholder($cacheKey = null)
    {
        if (is_null($cacheKey)) {
            $cacheKey = $this->getCacheKey();
        }

        return '<!--SID=' . $cacheKey . '-->';
    }

    /**
     * Collect and retrieve items tags.
     * Item should implement Mage_Core_Model_Abstract::getCacheIdTags method
     *
     * @param array|Varien_Data_Collection $items
     * @return array
     */
    public function getItemsTags($items)
    {
        $tags = [];
        /** @var Mage_Core_Model_Abstract $item */
        foreach ($items as $item) {
            $itemTags = $item->getCacheIdTags();
            if ($itemTags === false) {
                continue;
            }
            $tags = array_merge($tags, $itemTags);
        }
        return $tags;
    }

    /**
     * Checks is request Url is secure
     *
     * @return bool
     */
    protected function _isSecure()
    {
        return $this->_getApp()->getFrontController()->getRequest()->isSecure();
    }

    public function isModuleEnabled(?string $moduleName = null, string $helperAlias = 'core'): bool
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }

        return Mage::helper($helperAlias)->isModuleEnabled($moduleName);
    }

    /**
     * Check whether the module output is enabled
     *
     * Because many module blocks belong to Adminhtml module,
     * the feature "Disable module output" doesn't cover Admin area
     */
    public function isModuleOutputEnabled(?string $moduleName = null, string $helperAlias = 'core'): bool
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }

        return Mage::helper($helperAlias)->isModuleOutputEnabled($moduleName);
    }
}
