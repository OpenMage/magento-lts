<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Validator for custom layout update
 *
 * Validator checked XML validation and protected expressions
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Layout_Validator extends Mage_Core_Model_Validate_Abstract
{
    public const XML_PATH_LAYOUT_DISALLOWED_BLOCKS       = 'validators/custom_layout/disallowed_block';

    public const XML_INVALID                             = 'invalidXml';

    public const INVALID_TEMPLATE_PATH                   = 'invalidTemplatePath';

    public const INVALID_BLOCK_NAME                      = 'invalidBlockName';

    public const PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR = 'protectedAttrHelperInActionVar';

    public const INVALID_XML_OBJECT_EXCEPTION            = 'invalidXmlObject';

    /**
     * The Varien SimpleXml object
     *
     * @inheritDoc
     */
    protected $_value;

    /**
     * XPath expression for checking layout update
     *
     * @var array
     */
    protected $_disallowedXPathExpressions = [
        "*//*[local-name()='template']",
        '*//@template',
        // PHP resolves method names case-insensitively and accepts "Class::method" callables, so the
        // method attribute is compared after dropping any "Class::" part and lower-casing it
        "//*[translate(concat(substring-after(@method, '::'), substring(@method, 1 div not(contains(@method, '::')))),
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')='settemplate']",
        "//*[translate(concat(substring-after(@method, '::'), substring(@method, 1 div not(contains(@method, '::')))),
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')='setdatausingmethod']//*[contains(translate(text(),
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), 'template')]/../*",
    ];

    /**
     * @var string
     */
    protected $_xpathBlockValidationExpression = '';

    /**
     * Disallowed template name
     *
     * @var array
     */
    protected $_disallowedBlock = [];

    /**
     * Protected expressions
     *
     * @var array
     */
    protected $_protectedExpressions = [
        self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR => "//*[local-name()='action']/*[@helper]",
    ];

    public function __construct()
    {
        $this->_initMessageTemplates();
        $this->getDisallowedBlocks();
    }

    /**
     * Initialize messages templates with translating
     *
     * @return $this
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = [
                self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR
                    => Mage::helper('core')->__('Helper attributes should not be used in custom layout updates.'),
                self::XML_INVALID => Mage::helper('core')->__('XML data is invalid.'),
                self::INVALID_TEMPLATE_PATH => Mage::helper('core')->__(
                    'Invalid template path used in layout update.',
                ),
                self::INVALID_BLOCK_NAME => Mage::helper('core')->__('Disallowed block name for frontend.'),
                self::INVALID_XML_OBJECT_EXCEPTION
                    => Mage::helper('core')->__('XML object is not instance of "Varien_Simplexml_Element".'),
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDisallowedBlocks()
    {
        if (!count($this->_disallowedBlock)) {
            $disallowedBlockConfig = $this->_getDisallowedBlockConfigValue();
            if (is_array($disallowedBlockConfig)) {
                foreach (array_keys($disallowedBlockConfig) as $blockName) {
                    $this->_disallowedBlock[] = $blockName;
                }
            }
        }

        return $this->_disallowedBlock;
    }

    /**
     * @return mixed
     */
    protected function _getDisallowedBlockConfigValue()
    {
        return Mage::getStoreConfig(self::XML_PATH_LAYOUT_DISALLOWED_BLOCKS);
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  string|Varien_Simplexml_Element $value
     * @return bool
     * @throws Exception                       Throw exception when xml object is not
     *                                         instance of Varien_Simplexml_Element
     */
    public function isValid($value)
    {
        // Reset messages and give _error() a value: errors raised before the parsed value is set would
        // otherwise pass null to _createMessage() and throw a TypeError, which callers catching
        // Exception (e.g. Mage_Catalog_Model_Design) do not handle.
        $this->_setValue(is_string($value) ? $value : '');

        if (is_string($value)) {
            $value = trim($value);
            try {
                $value = new Varien_Simplexml_Element('<config>' . $value . '</config>');
            } catch (Exception) {
                $this->_error(self::XML_INVALID);
                return false;
            }
        } elseif (!($value instanceof Varien_Simplexml_Element)) {
            throw new Exception($this->_messageTemplates[self::INVALID_XML_OBJECT_EXCEPTION]);
        }

        // Layout XML never uses namespaces. SimpleXML (and so the layout generator) treats an element in a
        // default namespace as if it had none, while XPath name tests do not match it, so a namespaced
        // element could slip past the checks below. Reject any namespaced element or attribute outright.
        $namespacedNodes = $value->xpath("//*[namespace-uri() != ''] | //@*[namespace-uri() != '']");
        if (is_array($namespacedNodes) && $namespacedNodes !== []) {
            $this->_error(self::XML_INVALID);
            return false;
        }

        $disallowedBlocks = $value->xpath($this->getXpathBlockValidationExpression());
        if ((is_array($disallowedBlocks) && $disallowedBlocks !== []) || $this->_hasDisallowedBlock($value)) {
            $this->_error(self::INVALID_BLOCK_NAME);
            return false;
        }

        // if layout update declare custom templates then validate their paths
        if ($templatePaths = $value->xpath($this->getXpathValidationExpression())) {
            try {
                $this->validateTemplatePath($templatePaths);
            } catch (Exception) {
                $this->_error(self::INVALID_TEMPLATE_PATH);
                return false;
            }
        }

        $this->_setValue($value);

        foreach ($this->_protectedExpressions as $key => $xpr) {
            if ($this->_value->xpath($xpr)) {
                $this->_error($key);
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getProtectedExpressions()
    {
        return $this->_protectedExpressions;
    }

    /**
     * Returns xPath for validate incorrect path to template
     *
     * @return string xPath for validate incorrect path to template
     */
    public function getXpathValidationExpression()
    {
        return implode(' | ', $this->_disallowedXPathExpressions);
    }

    /**
     * @return array
     */
    public function getDisallowedXpathValidationExpression()
    {
        return $this->_disallowedXPathExpressions;
    }

    /**
     * Returns xPath for validate incorrect block name
     *
     * @return string xPath for validate incorrect block name
     */
    public function getXpathBlockValidationExpression()
    {
        if (!$this->_xpathBlockValidationExpression && count($this->_disallowedBlock)) {
            foreach ($this->_disallowedBlock as $key => $value) {
                $this->_xpathBlockValidationExpression .= $key > 0 ? ' | ' : '';
                $this->_xpathBlockValidationExpression
                    .= "//*[local-name()='block'][translate(@type, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = ";
                $this->_xpathBlockValidationExpression
                    .= "translate('{$value}', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')]";
            }
        }

        return $this->_xpathBlockValidationExpression;
    }

    /**
     * Whether the layout update declares a block whose class is disallowed
     *
     * The layout generator instantiates the "class" attribute when it is set and the "type"
     * attribute otherwise, resolves "module/name" aliases through the configuration and loads
     * class names case-insensitively, with or without a leading backslash. The XPath block test
     * only compares the literal "type" attribute, so every block is also checked here against
     * the class the generator would actually create, including subclasses of a disallowed class.
     */
    protected function _hasDisallowedBlock(Varien_Simplexml_Element $xml): bool
    {
        $disallowed = [];
        foreach ($this->getDisallowedBlocks() as $blockName) {
            $className = $this->_resolveBlockClassName((string) $blockName);
            if ($className !== '') {
                $disallowed[strtolower($className)] = $className;
            }
        }

        if ($disallowed === []) {
            return false;
        }

        $blocks = $xml->xpath("//*[local-name()='block']");
        foreach (is_array($blocks) ? $blocks : [] as $block) {
            foreach (['class', 'type'] as $attribute) {
                $className = $this->_resolveBlockClassName((string) $block[$attribute]);
                if ($className === '') {
                    continue;
                }

                if (isset($disallowed[strtolower($className)])) {
                    return true;
                }

                foreach ($disallowed as $disallowedClass) {
                    if ($this->_isSubclassOf($className, $disallowedClass)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Resolve a block type or class name to the class name the layout generator would load
     */
    protected function _resolveBlockClassName(string $blockType): string
    {
        $blockType = trim($blockType);
        if (str_contains($blockType, '/')) {
            $blockType = (string) Mage::getConfig()?->getBlockClassName($blockType);
        }

        return ltrim($blockType, '\\');
    }

    /**
     * Whether $className extends $parentClassName
     *
     * A class that is not loaded yet is only autoloaded when its name is a plain class name with a
     * file on the include path, the same condition under which the layout generator loads it.
     */
    protected function _isSubclassOf(string $className, string $parentClassName): bool
    {
        foreach ([$className, $parentClassName] as $name) {
            if (!class_exists($name, false)
                && (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) !== 1 || !is_string(mageFindClassFile($name)))
            ) {
                return false;
            }
        }

        return is_subclass_of($className, $parentClassName);
    }

    /**
     * Validate template path for preventing access to the directory above
     *
     * Every value that may reach setTemplate() is checked: the node itself or, for an action, each
     * of its arguments, both as written and decoded the way the layout may decode it (C-style
     * escapes, JSON for arguments listed in the "json" attribute). Template::fetchView() refuses
     * any ".." in a template path, so such paths are refused here as well.
     *
     * @throws Exception
     */
    public function validateTemplatePath(array $templatePaths)
    {
        /** @var Varien_Simplexml_Element $path */
        foreach ($templatePaths as $path) {
            foreach ($this->_getTemplatePathCandidates($path) as $candidate) {
                if (str_contains($candidate, '..')) {
                    throw new Exception();
                }
            }
        }
    }

    /**
     * @return list<string>
     */
    protected function _getTemplatePathCandidates(Varien_Simplexml_Element $path): array
    {
        $values = [];
        if ($path->hasChildren()) {
            foreach ($path->children() as $argument) {
                $values[] = (string) $argument;
            }
        } else {
            $values[] = (string) $path;
        }

        $candidates = [];
        foreach ($values as $value) {
            $candidates[] = $value;
            $candidates[] = stripcslashes(trim($value, '"'));
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $candidates[] = $decoded;
            }
        }

        return $candidates;
    }
}
