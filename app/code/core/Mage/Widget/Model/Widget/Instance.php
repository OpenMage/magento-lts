<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

use Monolog\Level;

/**
 * Widget Instance Model
 *
 * @package    Mage_Widget
 *
 * @method Mage_Widget_Model_Resource_Widget_Instance            _getResource()
 * @method Mage_Widget_Model_Resource_Widget_Instance_Collection getCollection()
 * @method array                                                 getPageGroups()
 * @method Mage_Widget_Model_Resource_Widget_Instance            getResource()
 * @method Mage_Widget_Model_Resource_Widget_Instance_Collection getResourceCollection()
 * @method int                                                   getSortOrder()
 * @method string                                                getTitle()
 * @method $this                                                 setPageGroups(array $value)
 * @method $this                                                 setSortOrder(int $value)
 * @method $this                                                 setStoreIds(string $value)
 * @method $this                                                 setTitle(string $value)
 * @method $this                                                 setWidgetParameters(string $value)
 */
class Mage_Widget_Model_Widget_Instance extends Mage_Core_Model_Abstract
{
    public const SPECIFIC_ENTITIES = 'specific';

    public const ALL_ENTITIES      = 'all';

    public const DEFAULT_LAYOUT_HANDLE            = 'default';

    public const PRODUCT_LAYOUT_HANDLE            = 'catalog_product_view';

    public const SINGLE_PRODUCT_LAYOUT_HANLDE     = 'PRODUCT_{{ID}}';

    public const PRODUCT_TYPE_LAYOUT_HANDLE       = 'PRODUCT_TYPE_{{TYPE}}';

    public const ANCHOR_CATEGORY_LAYOUT_HANDLE    = 'catalog_category_layered';

    public const NOTANCHOR_CATEGORY_LAYOUT_HANDLE = 'catalog_category_default';

    public const SINGLE_CATEGORY_LAYOUT_HANDLE    = 'CATEGORY_{{ID}}';

    public const XML_NODE_RELATED_CACHE = 'global/widget/related_cache_types';

    /**
     * Reasons the Mage_Widget 1.6.0.2 data upgrade removes a stored layout update
     *
     * @see getLayoutUpdateRemovalReason()
     */
    public const LAYOUT_UPDATE_UNDECLARED_TYPE = 'undeclared widget type';

    public const LAYOUT_UPDATE_UNEXPECTED_XML  = 'unexpected layout update XML';

    protected $_layoutHandles = [];

    protected $_specificEntitiesLayoutHandles = [];

    /**
     * @var Varien_Simplexml_Element
     */
    protected $_widgetConfigXml = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'widget_widget_instance';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_cacheTag = 'widget_instance';
        parent::_construct();
        $this->_init('widget/widget_instance');
        $this->_layoutHandles = [
            'anchor_categories' => self::ANCHOR_CATEGORY_LAYOUT_HANDLE,
            'notanchor_categories' => self::NOTANCHOR_CATEGORY_LAYOUT_HANDLE,
            'all_products' => self::PRODUCT_LAYOUT_HANDLE,
            'all_pages' => self::DEFAULT_LAYOUT_HANDLE,
        ];
        $this->_specificEntitiesLayoutHandles = [
            'anchor_categories' => self::SINGLE_CATEGORY_LAYOUT_HANDLE,
            'notanchor_categories' => self::SINGLE_CATEGORY_LAYOUT_HANDLE,
            'all_products' => self::SINGLE_PRODUCT_LAYOUT_HANLDE,
        ];
        foreach (array_keys(Mage_Catalog_Model_Product_Type::getTypes()) as $typeId) {
            $layoutHandle = str_replace('{{TYPE}}', $typeId, self::PRODUCT_TYPE_LAYOUT_HANDLE);
            $this->_layoutHandles[$typeId . '_products'] = $layoutHandle;
            $this->_specificEntitiesLayoutHandles[$typeId . '_products'] = self::SINGLE_PRODUCT_LAYOUT_HANLDE;
        }
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = [
            'type' => 'instance_type',
        ];
        return $this;
    }

    /**
     * Processing object before save data
     *
     * @inheritDoc
     */
    #[Override]
    protected function _beforeSave()
    {
        if (!$this->isTypeDeclared()) {
            Mage::log(
                sprintf(
                    'Refused to save widget instance %s: its type is not declared in any widget.xml.',
                    $this->_getLogLabel(),
                ),
                Level::Warning,
            );
            Mage::throwException(Mage::helper('widget')->__('Wrong widget type specified.'));
        }

        $pageGroupIds = [];
        $tmpPageGroups = [];
        $pageGroups = $this->getDataByKey('page_groups');
        if ($pageGroups) {
            foreach ($pageGroups as $pageGroup) {
                $pageGroupData = $this->validatePageGroup($pageGroup);
                if ($pageGroupData !== null) {
                    if ($pageGroupData['page_id']) {
                        $pageGroupIds[] = $pageGroupData['page_id'];
                    }

                    if ($pageGroup['page_group'] == 'pages') {
                        $layoutHandle = $pageGroupData['layout_handle'];
                    } else {
                        $layoutHandle = $this->_layoutHandles[$pageGroup['page_group']];
                    }

                    $pageGroupData['template'] ??= '';

                    $tmpPageGroup = [
                        'page_id' => $pageGroupData['page_id'],
                        'group' => $pageGroup['page_group'],
                        'layout_handle' => $layoutHandle,
                        'for' => $pageGroupData['for'],
                        'block_reference' => $pageGroupData['block'],
                        'entities' => '',
                        'layout_handle_updates' => [$layoutHandle],
                        'template' => $pageGroupData['template'] ? $pageGroupData['template'] : '',
                    ];
                    if ($pageGroupData['for'] == self::SPECIFIC_ENTITIES) {
                        $layoutHandleUpdates = [];
                        foreach (explode(',', $pageGroupData['entities']) as $entity) {
                            $layoutHandleUpdates[] = str_replace(
                                '{{ID}}',
                                $entity,
                                $this->_specificEntitiesLayoutHandles[$pageGroup['page_group']],
                            );
                        }

                        $tmpPageGroup['entities'] = $pageGroupData['entities'];
                        $tmpPageGroup['layout_handle_updates'] = $layoutHandleUpdates;
                    }

                    $tmpPageGroups[] = $tmpPageGroup;
                }
            }
        }

        if (is_array($this->getDataByKey('store_ids'))) {
            $this->setData('store_ids', implode(',', $this->getDataByKey('store_ids')));
        }

        if (is_array($this->getDataByKey('widget_parameters'))) {
            $this->setData('widget_parameters', serialize($this->getDataByKey('widget_parameters')));
        }

        $this->setData('page_groups', $tmpPageGroups);
        $this->setData('page_group_ids', $pageGroupIds);

        return parent::_beforeSave();
    }

    /**
     * Validate one entry of the page groups (display-on rules) before it is turned into layout updates
     *
     * The admin form and _beforeSave() share this check, so both treat a malformed entry the same way.
     *
     * @param  mixed               $pageGroup one page groups entry: its code under "page_group" and its data under that code
     * @return null|array<mixed>   the page group data, or null if the entry holds none
     * @throws Mage_Core_Exception if the page group is malformed
     */
    public function validatePageGroup($pageGroup): ?array
    {
        if (!is_array($pageGroup) || !isset($pageGroup['page_group'])) {
            return null;
        }

        $code = $pageGroup['page_group'];
        if (!is_string($code)) {
            throw Mage::exception('Mage_Core', Mage::helper('widget')->__('Wrong widget display option specified.'));
        }

        if (!isset($pageGroup[$code])) {
            return null;
        }

        $pageGroupData = $pageGroup[$code];
        if (!is_array($pageGroupData)) {
            throw Mage::exception('Mage_Core', Mage::helper('widget')->__('Wrong widget display option specified.'));
        }

        $this->_validatePageGroup($code, $pageGroupData);
        return $pageGroupData;
    }

    /**
     * Validate the data of one page group
     *
     * @param  string              $pageGroup     page group code, e.g. "all_pages" or "anchor_categories"
     * @param  mixed               $pageGroupData page group data posted for that code
     * @throws Mage_Core_Exception
     */
    protected function _validatePageGroup($pageGroup, $pageGroupData): void
    {
        if (!is_array($pageGroupData)
            || ($pageGroup !== 'pages' && !isset($this->_layoutHandles[$pageGroup]))
        ) {
            Mage::throwException(Mage::helper('widget')->__('Wrong widget display option specified.'));
        }

        $for = $pageGroupData['for'] ?? self::ALL_ENTITIES;
        if ($for !== self::ALL_ENTITIES && $for !== self::SPECIFIC_ENTITIES) {
            Mage::throwException(Mage::helper('widget')->__('Wrong widget display option specified.'));
        }

        if ($for === self::SPECIFIC_ENTITIES && !isset($this->_specificEntitiesLayoutHandles[$pageGroup])) {
            Mage::throwException(Mage::helper('widget')->__('Wrong widget display option specified.'));
        }

        if (!$this->isValidBlockReference($pageGroupData['block'] ?? '')
            || !$this->isValidTemplatePath($pageGroupData['template'] ?? '')
        ) {
            Mage::throwException(Mage::helper('widget')->__('Templatepath or block reference contain special characters.'));
        }
    }

    /**
     * Check that a block reference (layout block name) contains no characters with a meaning in XML or HTML
     *
     * @param mixed $blockReference
     */
    public function isValidBlockReference($blockReference): bool
    {
        return $this->_hasNoMarkupCharacters($blockReference);
    }

    /**
     * Check that a template path contains no characters with a meaning in XML or HTML
     *
     * This does not check where the path leads; the design package resolves it inside the theme.
     *
     * @param mixed $templatePath
     */
    public function isValidTemplatePath($templatePath): bool
    {
        return $this->_hasNoMarkupCharacters($templatePath);
    }

    /**
     * Check that a value is a string without characters that HTML escaping would change
     *
     * @param mixed $value
     */
    protected function _hasNoMarkupCharacters($value): bool
    {
        return is_string($value) && $value === htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Check that the widget type is declared in a widget.xml of an installed module
     *
     * Only declared widget types may be placed on the storefront. Instances saved with any other
     * type (for example by an older release that did not check it) can still be loaded and deleted,
     * but cannot be saved and get no layout update generated.
     */
    public function isTypeDeclared(): bool
    {
        $type = $this->getType();
        return is_string($type)
            && $type !== ''
            && Mage::getSingleton('widget/widget')->isWidgetTypeDeclared($type);
    }

    /**
     * Validate widget instance data
     *
     * @return string|true
     */
    public function validate()
    {
        if ($this->isCompleteToCreate()) {
            if (!$this->isTypeDeclared()) {
                return Mage::helper('widget')->__('Wrong widget type specified.');
            }

            return true;
        }

        return Mage::helper('widget')->__('Widget instance is not full complete to create.');
    }

    /**
     * Check if widget instance has required data (other data depends on it)
     *
     * @return bool
     */
    public function isCompleteToCreate()
    {
        return $this->getType() && $this->getPackageTheme();
    }

    /**
     * Setter
     * Prepare widget type
     *
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->setData('type', $type);
        $this->_prepareType();
        return $this;
    }

    /**
     * Getter
     * Prepare widget type
     *
     * @return string
     */
    public function getType()
    {
        $this->_prepareType();
        return $this->_getData('type');
    }

    /**
     * Replace '-' to '/', if was passed from request(GET request)
     *
     * @return $this
     */
    protected function _prepareType()
    {
        if (str_contains((string) $this->_getData('type'), '-')) {
            $this->setData('type', str_replace('-', '/', $this->_getData('type')));
        }

        return $this;
    }

    /**
     * Setter
     * Prepare widget package theme
     *
     * @param  string $packageTheme
     * @return $this
     */
    public function setPackageTheme($packageTheme)
    {
        $this->setData('package_theme', $packageTheme);
        return $this;
    }

    /**
     * Getter
     * Prepare widget package theme
     *
     * @return string
     */
    public function getPackageTheme()
    {
        return $this->_getData('package_theme');
    }

    /**
     * Replace '_' to '/', if was set from request(GET request)
     *
     * @return $this
     */
    #[Deprecated(message: 'after 1.6.1.0-alpha1')]
    protected function _preparePackageTheme()
    {
        return $this;
    }

    /**
     * Getter.
     * If not set return default
     *
     * @return string
     */
    public function getArea()
    {
        if (!$this->_getData('area')) {
            return Mage_Core_Model_Design_Package::DEFAULT_AREA;
        }

        return $this->_getData('area');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPackage()
    {
        if (!$this->_getData('package')) {
            $this->_parsePackageTheme();
        }

        return $this->_getData('package');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getTheme()
    {
        if (!$this->_getData('theme')) {
            $this->_parsePackageTheme();
        }

        return $this->_getData('theme');
    }

    /**
     * Parse packageTheme and set parsed package and theme
     *
     * @return $this
     */
    protected function _parsePackageTheme()
    {
        if ($this->getPackageTheme() && strpos($this->getPackageTheme(), '/')) {
            [$package, $theme] = explode('/', $this->getPackageTheme());
            $this->setData('package', $package);
            $this->setData('theme', $theme);
        }

        return $this;
    }

    /**
     * Getter
     * Explode to array if string is set
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (is_string($this->getDataByKey('store_ids'))) {
            return explode(',', $this->getDataByKey('store_ids'));
        }

        return $this->getDataByKey('store_ids');
    }

    /**
     * Getter
     * Unserialize if serialized string is set
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        if (is_string($this->getDataByKey('widget_parameters'))) {
            try {
                return Mage::helper('core/unserializeArray')->unserialize($this->getDataByKey('widget_parameters'));
            } catch (Exception $exception) {
                Mage::logException($exception);
            }
        }

        return (is_array($this->getDataByKey('widget_parameters'))) ? $this->getDataByKey('widget_parameters') : [];
    }

    /**
     * Retrieve option array of widget types
     *
     * @return array
     */
    public function getWidgetsOptionArray()
    {
        $widgets = [];
        $widgetsArr = Mage::getSingleton('widget/widget')->getWidgetsArray();
        foreach ($widgetsArr as $widget) {
            $widgets[] = [
                'value' => $widget['type'],
                'label' => $widget['name'],
            ];
        }

        return $widgets;
    }

    /**
     * Load widget XML config and merge with theme widget config
     *
     * @return null|Varien_Simplexml_Element
     */
    public function getWidgetConfig()
    {
        if ($this->_widgetConfigXml === null) {
            $this->_widgetConfigXml = Mage::getSingleton('widget/widget')
                ->getXmlElementByType($this->getType());
            if ($this->_widgetConfigXml) {
                $configFile = Mage::getSingleton('core/design_package')->getBaseDir([
                    '_area'    => $this->getArea(),
                    '_package' => $this->getPackage(),
                    '_theme'   => $this->getTheme(),
                    '_type'    => 'etc',
                ]) . DS . 'widget.xml';
                if (is_readable($configFile)) {
                    $themeWidgetsConfig = new Varien_Simplexml_Config();
                    $themeWidgetsConfig->loadFile($configFile);
                    if ($themeWidgetTypeConfig = $themeWidgetsConfig->getNode($this->_widgetConfigXml->getName())) {
                        $this->_widgetConfigXml->extend($themeWidgetTypeConfig);
                    }
                }
            }
        }

        return $this->_widgetConfigXml;
    }

    /**
     * Retrieve widget available templates
     *
     * @return array
     */
    public function getWidgetTemplates()
    {
        $templates = [];
        if ($this->getWidgetConfig() && ($configTemplates = $this->getWidgetConfig()->parameters->template)) {
            if ($configTemplates->values && $configTemplates->values->children()) {
                foreach ($configTemplates->values->children() as $name => $template) {
                    $helper = $template->getAttribute('module') ? $template->getAttribute('module') : 'widget';
                    $templates[(string) $name] = [
                        'value' => (string) $template->value,
                        'label' => Mage::helper($helper)->__((string) $template->label),
                    ];
                }
            } elseif ($configTemplates->value) {
                $templates['default'] = [
                    'value' => (string) $configTemplates->value,
                    'label' => Mage::helper('widget')->__('Default Template'),
                ];
            }
        }

        return $templates;
    }

    /**
     * Retrieve blocks that widget support
     *
     * @return array
     */
    public function getWidgetSupportedBlocks()
    {
        $blocks = [];
        if ($this->getWidgetConfig() && ($supportedBlocks = $this->getWidgetConfig()->supported_blocks)) {
            foreach ($supportedBlocks->children() as $block) {
                $blocks[] = (string) $block->block_name;
            }
        }

        return $blocks;
    }

    /**
     * Retrieve widget templates that supported by given block reference
     *
     * @param  string $blockReference
     * @return array
     */
    public function getWidgetSupportedTemplatesByBlock($blockReference)
    {
        $templates = [];
        $template = null;
        $widgetTemplates = $this->getWidgetTemplates();
        if ($this->getWidgetConfig()) {
            if (!($supportedBlocks = $this->getWidgetConfig()->supported_blocks)) {
                return $widgetTemplates;
            }

            foreach ($supportedBlocks->children() as $block) {
                if ((string) $block->block_name == $blockReference) {
                    if ($block->template && $block->template->children()) {
                        foreach ($block->template->children() as $template) {
                            if (isset($widgetTemplates[(string) $template])) {
                                $templates[] = $widgetTemplates[(string) $template];
                            }
                        }
                    } else {
                        $templates[] = $widgetTemplates[(string) $template];
                    }
                }
            }
        } else {
            return $widgetTemplates;
        }

        return $templates;
    }

    /**
     * Generate layout update xml
     *
     * Nothing is generated for an incomplete new instance, for an unreadable template, or for a
     * type that no widget.xml declares (_beforeSave() refuses to save such an instance, and logs it).
     *
     * @param  string $blockReference
     * @param  string $templatePath
     * @return string
     */
    public function generateLayoutUpdateXml($blockReference, $templatePath = '')
    {
        $templatePath = (string) $templatePath;
        if (!$this->isValidTemplatePath($templatePath) || !$this->isValidBlockReference($blockReference)) {
            Mage::throwException(Mage::helper('widget')->__('Templatepath or block reference contain special characters.'));
        }

        if ((!$this->getId() && !$this->isCompleteToCreate()) || !$this->isTypeDeclared()) {
            return '';
        }

        $templateFilename = Mage::getSingleton('core/design_package')->getTemplateFilename($templatePath, [
            '_area'    => $this->getArea(),
            '_package' => $this->getPackage(),
            '_theme'   => $this->getTheme(),
        ]);
        if ($templatePath && !is_readable($templateFilename)) {
            return '';
        }

        $parameters = $this->getWidgetParameters();
        $xml = '<reference name="' . $this->_escapeXml($blockReference) . '">';
        $template = '';
        if (isset($parameters['template'])) {
            unset($parameters['template']);
        }

        if ($templatePath) {
            $template = ' template="' . $this->_escapeXml($templatePath) . '"';
        }

        $hash = Mage::helper('core')->uniqHash();
        $xml .= '<block type="' . $this->_escapeXml($this->getType()) . '" name="' . $this->_escapeXml($hash) . '"' . $template . '>';
        foreach ($parameters as $name => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }

            if ($name && strlen((string) $value)) {
                $xml .= '<action method="setData">'
                    . '<name>' . $this->_escapeXml($name) . '</name>'
                    . '<value>' . $this->_escapeXml($value) . '</value>'
                    . '</action>';
            }
        }

        return $xml . '</block></reference>';
    }

    /**
     * Identify this instance in log messages
     */
    protected function _getLogLabel(): string
    {
        $id = $this->getId();
        return $id === null || $id === '' ? '(new)' : '#' . $id;
    }

    /**
     * Check that a stored layout update has exactly the shape generateLayoutUpdateXml() produces
     *
     * That is one <reference> holding one <block> of this instance's (declared) type, whose only
     * children are <action method="setData"> elements with a <name> and a <value>. Layout updates
     * of any other shape were not produced by the widget generator for this type, for example
     * because a type with markup in it was saved before types were checked.
     *
     * @param string $xml layout update XML as stored in core_layout_update.xml
     */
    public function isGeneratedLayoutUpdateXml($xml): bool
    {
        if (!is_string($xml)) {
            return false;
        }

        if (trim($xml) === '') {
            // Generated when the instance was incomplete or its template unreadable; it adds nothing
            return true;
        }

        return $this->isTypeDeclared() && $this->getGeneratedLayoutUpdateBlockType($xml) === $this->getType();
    }

    /**
     * Why the data upgrade removes a stored layout update of this instance, or null if it is kept
     *
     * @param  string                                                                      $xml layout update XML as stored in core_layout_update.xml
     * @return null|self::LAYOUT_UPDATE_UNDECLARED_TYPE|self::LAYOUT_UPDATE_UNEXPECTED_XML
     */
    public function getLayoutUpdateRemovalReason($xml): ?string
    {
        if ($this->isGeneratedLayoutUpdateXml($xml)) {
            return null;
        }

        return $this->isTypeDeclared() ? self::LAYOUT_UPDATE_UNEXPECTED_XML : self::LAYOUT_UPDATE_UNDECLARED_TYPE;
    }

    /**
     * Block type of a layout update that has the shape generateLayoutUpdateXml() produces, or null for any other XML
     *
     * The type itself is not checked; isGeneratedLayoutUpdateXml() compares it with this instance's type.
     *
     * @param string $xml layout update XML as stored in core_layout_update.xml
     */
    public function getGeneratedLayoutUpdateBlockType($xml): ?string
    {
        if (!is_string($xml) || trim($xml) === '') {
            return null;
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML('<root>' . $xml . '</root>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (!$loaded || !$document->documentElement instanceof DOMElement) {
            return null;
        }

        $references = $this->_getChildElements($document->documentElement);
        if ($references === null || count($references) !== 1
            || !$this->_isPlainElement($references[0], 'reference', ['name'], ['name'])
            || !$this->isValidBlockReference($references[0]->getAttribute('name'))
        ) {
            return null;
        }

        $blocks = $this->_getChildElements($references[0]);
        if ($blocks === null || count($blocks) !== 1
            || !$this->_isPlainElement($blocks[0], 'block', ['type', 'name', 'template'], ['type', 'name'])
            || ($blocks[0]->hasAttribute('template') && !$this->isValidTemplatePath($blocks[0]->getAttribute('template')))
        ) {
            return null;
        }

        $actions = $this->_getChildElements($blocks[0]);
        if ($actions === null) {
            return null;
        }

        foreach ($actions as $action) {
            if (!$this->_isPlainElement($action, 'action', ['method'], ['method'])
                || $action->getAttribute('method') !== 'setData'
            ) {
                return null;
            }

            $arguments = $this->_getChildElements($action);
            if ($arguments === null || count($arguments) !== 2
                || !$this->_isPlainElement($arguments[0], 'name', [])
                || !$this->_isPlainElement($arguments[1], 'value', [])
                || $this->_getChildElements($arguments[0]) !== []
                || $this->_getChildElements($arguments[1]) !== []
                // The generator never sets the template through a parameter, nor a parameter without a name
                || in_array($arguments[0]->textContent, ['', 'template'], true)
            ) {
                return null;
            }
        }

        return $blocks[0]->getAttribute('type');
    }

    /**
     * Child elements of a node, or null if it has children other than elements, text and CDATA
     *
     * Text is only allowed where it cannot hold markup: whitespace between elements, or the text
     * content of an element without element children (checked by the caller).
     *
     * @return null|list<DOMElement>
     */
    protected function _getChildElements(DOMElement $parent): ?array
    {
        $elements = [];
        $hasText = false;
        foreach ($parent->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $elements[] = $child;
            } elseif ($child instanceof DOMText) {
                // DOMCdataSection extends DOMText
                $hasText = $hasText || trim($child->data) !== '';
            } else {
                return null;
            }
        }

        if ($hasText && $elements !== []) {
            return null;
        }

        return $elements;
    }

    /**
     * Check an element's name, that it has no namespace, that it only has allowed attributes, and has the required ones
     *
     * @param string[] $allowedAttributes
     * @param string[] $requiredAttributes
     */
    protected function _isPlainElement(DOMElement $element, string $name, array $allowedAttributes, array $requiredAttributes = []): bool
    {
        if ($element->namespaceURI !== null || $element->nodeName !== $name) {
            return false;
        }

        foreach ($element->attributes ?? [] as $attribute) {
            if (!$attribute instanceof DOMAttr
                || $attribute->namespaceURI !== null
                || !in_array($attribute->nodeName, $allowedAttributes, true)
            ) {
                return false;
            }
        }

        foreach ($requiredAttributes as $attribute) {
            if ($element->getAttribute($attribute) === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Escape a value for use in layout update XML text or a double- or single-quoted attribute
     *
     * @param  mixed  $value
     * @return string
     */
    protected function _escapeXml($value)
    {
        $escaped = htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
        // Control characters are not allowed in XML 1.0 even when escaped, and would make the update unreadable
        return (string) preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', "\u{FFFD}", $escaped);
    }

    /**
     * Invalidate related cache types
     *
     * @return $this
     */
    protected function _invalidateCache()
    {
        $types = Mage::getConfig()->getNode(self::XML_NODE_RELATED_CACHE);
        if ($types) {
            $types = $types->asArray();
            Mage::app()->getCacheInstance()->invalidateType(array_keys($types));
        }

        return $this;
    }

    /**
     * Invalidate related cache if instance contain layout updates
     */
    #[Override]
    protected function _afterSave()
    {
        if ($this->dataHasChangedFor('page_groups') || $this->dataHasChangedFor('widget_parameters')) {
            $this->_invalidateCache();
        }

        return parent::_afterSave();
    }

    /**
     * Invalidate related cache if instance contain layout updates
     */
    #[Override]
    protected function _beforeDelete()
    {
        if ($this->getPageGroups()) {
            $this->_invalidateCache();
        }

        return parent::_beforeDelete();
    }
}
