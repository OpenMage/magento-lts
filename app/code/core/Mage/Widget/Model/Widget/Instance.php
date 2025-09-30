<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance Model
 *
 * @package    Mage_Widget
 *
 * @method Mage_Widget_Model_Resource_Widget_Instance _getResource()
 * @method Mage_Widget_Model_Resource_Widget_Instance getResource()
 * @method Mage_Widget_Model_Resource_Widget_Instance_Collection getCollection()
 *
 * @method array getPageGroups()
 * @method $this setPageGroups(array $value)
 * @method $this setStoreIds(string $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method $this setWidgetParameters(string $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
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
     * Internal Constructor
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
        foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $layoutHandle = str_replace('{{TYPE}}', $typeId, self::PRODUCT_TYPE_LAYOUT_HANDLE);
            $this->_layoutHandles[$typeId . '_products'] = $layoutHandle;
            $this->_specificEntitiesLayoutHandles[$typeId . '_products'] = self::SINGLE_PRODUCT_LAYOUT_HANLDE;
        }
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
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
    protected function _beforeSave()
    {
        $pageGroupIds = [];
        $tmpPageGroups = [];
        $pageGroups = $this->getData('page_groups');
        if ($pageGroups) {
            foreach ($pageGroups as $pageGroup) {
                if (isset($pageGroup[$pageGroup['page_group']])) {
                    $pageGroupData = $pageGroup[$pageGroup['page_group']];
                    if ($pageGroupData['page_id']) {
                        $pageGroupIds[] = $pageGroupData['page_id'];
                    }
                    if ($pageGroup['page_group'] == 'pages') {
                        $layoutHandle = $pageGroupData['layout_handle'];
                    } else {
                        $layoutHandle = $this->_layoutHandles[$pageGroup['page_group']];
                    }
                    if (!isset($pageGroupData['template'])) {
                        $pageGroupData['template'] = '';
                    }
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
        if (is_array($this->getData('store_ids'))) {
            $this->setData('store_ids', implode(',', $this->getData('store_ids')));
        }
        if (is_array($this->getData('widget_parameters'))) {
            $this->setData('widget_parameters', serialize($this->getData('widget_parameters')));
        }
        $this->setData('page_groups', $tmpPageGroups);
        $this->setData('page_group_ids', $pageGroupIds);

        return parent::_beforeSave();
    }

    /**
     * Validate widget instance data
     *
     * @return string|bool
     */
    public function validate()
    {
        if ($this->isCompleteToCreate()) {
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
        return (bool) ($this->getType() && $this->getPackageTheme());
    }

    /**
     * Setter
     * Prepare widget type
     *
     * @param string $type
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
     * @param string $packageTheme
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
     * @deprecated after 1.6.1.0-alpha1
     *
     * @return $this
     */
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
        if (is_string($this->getData('store_ids'))) {
            return explode(',', $this->getData('store_ids'));
        }
        return $this->getData('store_ids');
    }

    /**
     * Getter
     * Unserialize if serialized string is set
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        if (is_string($this->getData('widget_parameters'))) {
            try {
                return Mage::helper('core/unserializeArray')->unserialize($this->getData('widget_parameters'));
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return (is_array($this->getData('widget_parameters'))) ? $this->getData('widget_parameters') : [];
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
     * @return Varien_Simplexml_Element|null
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
     * @param string $blockReference
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
     * @param string $blockReference
     * @param string $templatePath
     * @return string
     */
    public function generateLayoutUpdateXml($blockReference, $templatePath = '')
    {
        if ($templatePath !== htmlspecialchars($templatePath, ENT_QUOTES | ENT_HTML5)
            || $blockReference !== htmlspecialchars($blockReference, ENT_QUOTES | ENT_HTML5)
        ) {
            Mage::throwException('Templatepath or block reference contain special characters.');
        }

        $templateFilename = Mage::getSingleton('core/design_package')->getTemplateFilename($templatePath, [
            '_area'    => $this->getArea(),
            '_package' => $this->getPackage(),
            '_theme'   => $this->getTheme(),
        ]);
        if (!$this->getId() && !$this->isCompleteToCreate()
            || ($templatePath && !is_readable($templateFilename))
        ) {
            return '';
        }
        $parameters = $this->getWidgetParameters();
        $xml = '<reference name="' . $blockReference . '">';
        $template = '';
        if (isset($parameters['template'])) {
            unset($parameters['template']);
        }
        if ($templatePath) {
            $template = ' template="' . $templatePath . '"';
        }

        $hash = Mage::helper('core')->uniqHash();
        $xml .= '<block type="' . $this->getType() . '" name="' . $hash . '"' . $template . '>';
        foreach ($parameters as $name => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            if ($name && strlen((string) $value)) {
                $xml .= '<action method="setData">'
                    . '<name>' . $name . '</name>'
                    . '<value>' . Mage::helper('widget')->escapeHtml($value) . '</value>'
                    . '</action>';
            }
        }

        return $xml . '</block></reference>';
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
    protected function _beforeDelete()
    {
        if ($this->getPageGroups()) {
            $this->_invalidateCache();
        }
        return parent::_beforeDelete();
    }
}
