<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System config form block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public const SCOPE_DEFAULT = 'default';

    public const SCOPE_WEBSITES = 'websites';

    public const SCOPE_STORES   = 'stores';

    public const SCOPE_ENV      = 'env';

    /**
     * Config data array
     *
     * @var array
     */
    protected $_configData;

    /**
     * Adminhtml config data instance
     *
     * @var Mage_Adminhtml_Model_Config_Data
     */
    protected $_configDataObject;

    /**
     * @var Varien_Simplexml_Element
     */
    protected $_configRoot;

    /**
     * @var Mage_Adminhtml_Model_Config
     */
    protected $_configFields;

    /**
     * @var false|Mage_Adminhtml_Block_System_Config_Form_Fieldset
     */
    protected $_defaultFieldsetRenderer;

    /**
     * @var false|Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected $_defaultFieldRenderer;

    /**
     * @var array
     */
    protected $_fieldsets = [];

    /**
     * Translated scope labels
     *
     * @var array
     */
    protected $_scopeLabels = [];

    public function __construct()
    {
        parent::__construct();
        $this->_scopeLabels = [
            self::SCOPE_DEFAULT  => Mage::helper('adminhtml')->__('[GLOBAL]'),
            self::SCOPE_WEBSITES => Mage::helper('adminhtml')->__('[WEBSITE]'),
            self::SCOPE_STORES   => Mage::helper('adminhtml')->__('[STORE VIEW]'),
            self::SCOPE_ENV      => Mage::helper('adminhtml')->__('[ENV]'),
        ];
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _initObjects()
    {
        $this->_configDataObject = Mage::getSingleton('adminhtml/config_data');
        $this->_configRoot = $this->_configDataObject->getConfigRoot();
        $this->_configData = $this->_configDataObject->load();

        $this->_configFields = Mage::getSingleton('adminhtml/config');

        $this->_defaultFieldsetRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_fieldset');
        $this->_defaultFieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function initForm()
    {
        $this->_initObjects();

        $form = new Varien_Data_Form();

        $sections = $this->_configFields->getSection(
            $this->getSectionCode(),
            $this->getWebsiteCode(),
            $this->getStoreCode(),
        );
        if (empty($sections)) {
            $sections = [];
        }

        foreach ($sections as $section) {
            /** @var Mage_Core_Model_Config_Element $section */
            if (!$this->_canShowField($section)) {
                continue;
            }

            foreach ($section->groups as $groups) {
                $groups = (array) $groups;
                usort($groups, [$this, '_sortForm']);

                foreach ($groups as $group) {
                    /** @var Mage_Core_Model_Config_Element $group */
                    if (!$this->_canShowField($group)) {
                        continue;
                    }

                    $this->_initGroup($form, $group, $section);
                }
            }
        }

        $this->setForm($form);
        return $this;
    }

    /**
     * Init config group
     *
     * @param  Varien_Data_Form                       $form
     * @param  Mage_Core_Model_Config_Element         $group
     * @param  Mage_Core_Model_Config_Element         $section
     * @param  null|Varien_Data_Form_Element_Fieldset $parentElement
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _initGroup($form, $group, $section, $parentElement = null)
    {
        /** @var Mage_Adminhtml_Block_System_Config_Form_Fieldset $fieldsetRenderer */
        $fieldsetRenderer = $group->frontend_model
            ? Mage::getBlockSingleton((string) $group->frontend_model)
            : $this->_defaultFieldsetRenderer;
        $fieldsetRenderer->setForm($this)
            ->setConfigData($this->_configData);

        if ($this->_configFields->hasChildren($group, $this->getWebsiteCode(), $this->getStoreCode())) {
            $helperName = $this->_configFields->getAttributeModule($section, $group);
            $fieldsetConfig = ['legend' => Mage::helper($helperName)->__((string) $group->label)];
            if (!empty($group->comment)) {
                $fieldsetConfig['comment'] = $this->_prepareGroupComment($group, $helperName);
            }

            if (!empty($group->expanded)) {
                $fieldsetConfig['expanded'] = (bool) $group->expanded;
            }

            $fieldset = new Varien_Data_Form_Element_Fieldset($fieldsetConfig);
            $fieldset->setId($section->getName() . '_' . $group->getName())
                ->setRenderer($fieldsetRenderer)
                ->setGroup($group);

            if ($parentElement) {
                $fieldset->setIsNested(true);
                $parentElement->addElement($fieldset);
            } else {
                $form->addElement($fieldset);
            }

            $this->_prepareFieldOriginalData($fieldset, $group);
            $this->_addElementTypes($fieldset);

            $this->_fieldsets[$group->getName()] = $fieldset;

            if ($group->clone_fields) {
                if ($group->clone_model) {
                    $cloneModel = Mage::getModel((string) $group->clone_model);
                } else {
                    Mage::throwException($this->__('Config form fieldset clone model required to be able to clone fields'));
                }

                foreach ($cloneModel->getPrefixes() as $prefix) {
                    $this->initFields($fieldset, $group, $section, $prefix['field'], $prefix['label']);
                }
            } else {
                $this->initFields($fieldset, $group, $section);
            }
        }
    }

    /**
     * Return dependency block object
     *
     * @return Mage_Adminhtml_Block_Widget_Form_Element_Dependence
     */
    protected function _getDependence()
    {
        if (!$this->getChild('element_dependense')) {
            $this->setChild(
                'element_dependense',
                $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'),
            );
        }

        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $child */
        $child = $this->getChild('element_dependense');
        return $child;
    }

    /**
     * Init fieldset fields
     *
     * @param  Varien_Data_Form_Element_Fieldset $fieldset
     * @param  Mage_Core_Model_Config_Element    $group
     * @param  Mage_Core_Model_Config_Element    $section
     * @param  string                            $fieldPrefix
     * @param  string                            $labelPrefix
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function initFields($fieldset, $group, $section, $fieldPrefix = '', $labelPrefix = '')
    {
        if (!$this->_configDataObject) {
            $this->_initObjects();
        }

        // Extends for config data
        $configDataAdditionalGroups = [];

        foreach ($group->fields as $elements) {
            $elements = (array) $elements;
            // sort either by sort_order or by child node values bypassing the sort_order
            if ($group->sort_fields && $group->sort_fields->by) {
                $fieldset->setSortElementsByAttribute(
                    (string) $group->sort_fields->by,
                    $group->sort_fields->direction_desc ? SORT_DESC : SORT_ASC,
                );
            } else {
                usort($elements, [$this, '_sortForm']);
            }

            foreach ($elements as $element) {
                if (!$this->_canShowField($element)) {
                    continue;
                }

                if ((string) $element->getAttribute('type') == 'group') {
                    $this->_initGroup($fieldset->getForm(), $element, $section, $fieldset);
                    continue;
                }

                /**
                 * Look for custom defined field path
                 */
                $path = (string) $element->config_path;
                if (empty($path)) {
                    $path = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $element->getName();
                } elseif (strrpos($path, '/') > 0) {
                    // Extend config data with new section group
                    $groupPath = substr($path, 0, strrpos($path, '/'));
                    if (!isset($configDataAdditionalGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject->extendConfig(
                            $groupPath,
                            false,
                            $this->_configData,
                        );
                        $configDataAdditionalGroups[$groupPath] = true;
                    }
                }

                $data = $this->_configDataObject->getConfigDataValue($path, $inherit, $this->_configData);
                /** @var Mage_Adminhtml_Block_System_Config_Form_Field $fieldRenderer */
                $fieldRenderer = $element->frontend_model
                    ? Mage::getBlockSingleton((string) $element->frontend_model)
                    : $this->_defaultFieldRenderer;

                $fieldRenderer->setForm($this);
                $fieldRenderer->setConfigData($this->_configData);

                $helperName = $this->_configFields->getAttributeModule($section, $group, $element);
                $fieldType  = (string) $element->frontend_type ? (string) $element->frontend_type : 'text';
                $name  = 'groups[' . $group->getName() . '][fields][' . $fieldPrefix . $element->getName() . '][value]';
                $label =  Mage::helper($helperName)->__($labelPrefix) . ' '
                    . Mage::helper($helperName)->__((string) $element->label);
                $hint  = (string) $element->hint ? Mage::helper($helperName)->__((string) $element->hint) : '';

                $helper = Mage::helper('adminhtml/config');
                $backendClass = $helper->getBackendModelByFieldConfig($element);
                if ($backendClass) {
                    $model = Mage::getModel($backendClass);
                    if (!$model instanceof Mage_Core_Model_Config_Data) {
                        Mage::throwException('Invalid config field backend model: ' . $element->backend_model);
                    }

                    $model->setPath($path)
                        ->setValue($data)
                        ->setWebsite($this->getWebsiteCode())
                        ->setStore($this->getStoreCode())
                        ->afterLoad();
                    $data = $model->getValue();
                }

                $comment = $this->_prepareFieldComment($element, $helperName, $data);
                $tooltip = $this->_prepareFieldTooltip($element, $helperName);
                $id = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $element->getName();

                if ($element->depends) {
                    foreach ($element->depends->children() as $dependent) {
                        /** @var Mage_Core_Model_Config_Element $dependent */

                        if (isset($dependent->fieldset)) {
                            $dependentFieldGroupName = (string) $dependent->fieldset;
                            if (!isset($this->_fieldsets[$dependentFieldGroupName])) {
                                $dependentFieldGroupName = $group->getName();
                            }
                        } else {
                            $dependentFieldGroupName = $group->getName();
                        }

                        $dependentFieldNameValue = $dependent->getName();
                        $dependentFieldGroup = $dependentFieldGroupName == $group->getName()
                            ? $group
                            : $this->_fieldsets[$dependentFieldGroupName]->getGroup();

                        $dependentId = $section->getName()
                            . '_' . $dependentFieldGroupName
                            . '_' . $fieldPrefix
                            . $dependentFieldNameValue;
                        $shouldBeAddedDependence = true;
                        $dependentValue = (string) ($dependent->value ?? $dependent);
                        if (isset($dependent['separator'])) {
                            $dependentValue = explode((string) $dependent['separator'], $dependentValue);
                        }

                        $dependentFieldName = $fieldPrefix . $dependent->getName();
                        $dependentField     = $dependentFieldGroup->fields->$dependentFieldName;
                        /*
                         * If dependent field can't be shown in current scope and real dependent config value
                         * is not equal to preferred one, then hide dependence fields by adding dependence
                         * based on not shown field (not rendered field)
                         */
                        if (!$this->_canShowField($dependentField)) {
                            $dependentFullPath = $section->getName()
                                . '/' . $dependentFieldGroupName
                                . '/' . $fieldPrefix
                                . $dependent->getName();
                            $dependentValueInStore = Mage::getStoreConfig($dependentFullPath, $this->getStoreCode());
                            if (is_array($dependentValue)) {
                                $shouldBeAddedDependence = !in_array($dependentValueInStore, $dependentValue);
                            } else {
                                $shouldBeAddedDependence = $dependentValue != $dependentValueInStore;
                            }
                        }

                        if ($shouldBeAddedDependence) {
                            $this->_getDependence()
                                ->addFieldMap($id, $id)
                                ->addFieldMap($dependentId, $dependentId)
                                ->addFieldDependence($id, $dependentId, $dependentValue);
                        }
                    }
                }

                $sharedClass = '';
                if ($element->shared && $element->config_path) {
                    $sharedClass = ' shared shared-' . str_replace('/', '-', $element->config_path);
                }

                $requiresClass = '';
                if ($element->requires) {
                    $requiresClass = ' requires';
                    foreach (explode(',', $element->requires) as $groupName) {
                        $requiresClass .= ' requires-' . $section->getName() . '_' . $groupName;
                    }
                }

                $elementFieldData = [
                    'name'                  => $name,
                    'label'                 => $label,
                    'comment'               => $comment,
                    'tooltip'               => $tooltip,
                    'hint'                  => $hint,
                    'value'                 => $data,
                    'inherit'               => $inherit,
                    'class'                 => $element->frontend_class . $sharedClass . $requiresClass,
                    'field_config'          => $element,
                    'config_path'           => $path,
                    'scope'                 => $this->getScope(),
                    'scope_id'              => $this->getScopeId(),
                    'scope_label'           => $this->getScopeLabel($element),
                    'can_use_default_value' => $this->canUseDefaultValue($element->show_in_default),
                    'can_use_website_value' => $this->canUseWebsiteValue($element->show_in_website),
                ];
                if ($this->isOverwrittenByEnvVariable($path)) {
                    $elementFieldData['scope_label'] = $this->_scopeLabels[self::SCOPE_ENV];
                    $elementFieldData['disabled'] = 1;
                    $elementFieldData['can_use_default_value'] = 0;
                    $elementFieldData['can_use_website_value'] = 0;
                }

                $field = $fieldset->addField($id, $fieldType, $elementFieldData);
                $this->_prepareFieldOriginalData($field, $element);

                if (isset($element->validate)) {
                    $field->addClass($element->validate);
                }

                if (isset($element->frontend_type)
                    && (string) $element->frontend_type === 'multiselect'
                    && isset($element->can_be_empty)
                ) {
                    $field->setCanBeEmpty(true);
                }

                $field->setRenderer($fieldRenderer);

                if ($element->source_model) {
                    // determine callback for the source model
                    $factoryName = (string) $element->source_model;
                    $method = false;
                    if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
                        array_shift($matches);
                        [$factoryName, $method] = array_values($matches);
                    }

                    $sourceModel = Mage::getSingleton($factoryName);
                    if (!$sourceModel) {
                        Mage::throwException("Source model '{$factoryName}' is not found");
                    }

                    if ($sourceModel instanceof Varien_Object) {
                        $sourceModel->setPath($path);
                    }

                    $optionArray = [];
                    if ($method) {
                        if ($fieldType == 'multiselect') {
                            $optionArray = $sourceModel->$method();
                        } else {
                            foreach ($sourceModel->$method() as $value => $label) {
                                $optionArray[] = ['label' => $label, 'value' => $value];
                            }
                        }
                    } elseif (method_exists($sourceModel, 'toOptionArray')) {
                        $optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
                    } else {
                        Mage::throwException("Missing method 'toOptionArray()' in source model '{$factoryName}'");
                    }

                    $field->setValues($optionArray);
                }
            }
        }

        return $this;
    }

    /**
     * Return config root node for current scope
     *
     * @return Varien_Simplexml_Element
     */
    public function getConfigRoot()
    {
        if (empty($this->_configRoot)) {
            $this->_configRoot = Mage::getSingleton('adminhtml/config_data')->getConfigRoot();
        }

        return $this->_configRoot;
    }

    /**
     * Set "original_data" array to the element, composed from nodes with scalar values
     *
     * @param Varien_Data_Form_Element_Abstract $field
     * @param Varien_Simplexml_Element          $xmlElement
     */
    protected function _prepareFieldOriginalData($field, $xmlElement)
    {
        $originalData = [];
        foreach ($xmlElement as $key => $value) {
            if (!$value->hasChildren()) {
                $originalData[$key] = (string) $value;
            }
        }

        $field->setOriginalData($originalData);
    }

    /**
     * Support models "getCommentText" method for field note generation
     *
     * @param  Mage_Core_Model_Config_Element $element
     * @param  string                         $helper
     * @return string
     */
    protected function _prepareFieldComment($element, $helper, $currentValue)
    {
        $comment = '';
        if ($element->comment) {
            $commentInfo = $element->comment->asArray();
            if (is_array($commentInfo)) {
                if (isset($commentInfo['model'])) {
                    $model = Mage::getModel($commentInfo['model']);
                    if (method_exists($model, 'getCommentText')) {
                        $comment = $model->getCommentText($element, $currentValue);
                    }
                }
            } else {
                $comment = Mage::helper($helper)->__($commentInfo);
            }
        }

        return $comment;
    }

    /**
     * Support models "getCommentText" method for group note generation
     *
     * @param  Mage_Core_Model_Config_Element $element
     * @param  string                         $helper
     * @return string
     */
    protected function _prepareGroupComment($element, $helper)
    {
        return $this->_prepareFieldComment($element, $helper, null);
    }

    /**
     * Prepare additional comment for field like tooltip
     *
     * @param  Mage_Core_Model_Config_Element $element
     * @param  string                         $helper
     * @return string
     */
    protected function _prepareFieldTooltip($element, $helper)
    {
        if ($element->tooltip) {
            return Mage::helper($helper)->__((string) $element->tooltip);
        }

        if ($element->tooltip_block) {
            return $this->getLayout()->createBlock((string) $element->tooltip_block)->toHtml();
        }

        return '';
    }

    /**
     * Append dependence block at then end of form block
     */
    protected function _afterToHtml($html)
    {
        if ($this->_getDependence()) {
            $html .= $this->_getDependence()->toHtml();
        }

        return parent::_afterToHtml($html);
    }

    /**
     * @param  Varien_Simplexml_Element $a
     * @param  Varien_Simplexml_Element $b
     * @return int
     */
    protected function _sortForm($a, $b)
    {
        return (int) $a->sort_order <=> (int) $b->sort_order;
    }

    /**
     * @param  Mage_Core_Model_Config_Element $field
     * @return bool
     * @throws Exception
     */
    public function canUseDefaultValue($field)
    {
        if ($this->getScope() == self::SCOPE_STORES && $field) {
            return true;
        }

        if ($this->getScope() == self::SCOPE_WEBSITES && $field) {
            return true;
        }

        return false;
    }

    /**
     * @param  Mage_Core_Model_Config_Element $field
     * @return bool
     * @throws Exception
     */
    public function canUseWebsiteValue($field)
    {
        if ($this->getScope() == self::SCOPE_STORES && $field) {
            return true;
        }

        return false;
    }

    /**
     * Checking field visibility
     *
     * @param  Mage_Core_Model_Config_Element $field
     * @return bool
     * @throws Exception
     */
    protected function _canShowField($field)
    {
        $ifModuleEnabled = trim((string) $field->if_module_enabled);
        if ($ifModuleEnabled && !$this->isModuleEnabled($ifModuleEnabled)) {
            return false;
        }

        $aclResource = trim((string) $field->acl_resource);
        if ($aclResource && !Mage::getSingleton('admin/session')->isAllowed($aclResource)) {
            return false;
        }

        return match ($this->getScope()) {
            self::SCOPE_DEFAULT => (bool) (int) $field->show_in_default,
            self::SCOPE_WEBSITES => (bool) (int) $field->show_in_website,
            self::SCOPE_STORES => (bool) (int) $field->show_in_store,
            default => true,
        };
    }

    /**
     * Retrieve current scope
     *
     * @return string
     * @throws Exception
     */
    public function getScope()
    {
        $scope = $this->getData('scope');
        if (is_null($scope)) {
            if ($this->getStoreCode()) {
                $scope = self::SCOPE_STORES;
            } elseif ($this->getWebsiteCode()) {
                $scope = self::SCOPE_WEBSITES;
            } else {
                $scope = self::SCOPE_DEFAULT;
            }

            $this->setScope($scope);
        }

        return $scope;
    }

    /**
     * Returns true if element was overwritten by ENV variable
     *
     * @throws Exception
     */
    public function isOverwrittenByEnvVariable(string $path): bool
    {
        /** @var Mage_Core_Helper_EnvironmentConfigLoader $environmentConfigLoaderHelper */
        $environmentConfigLoaderHelper = Mage::helper('core/environmentConfigLoader');

        $scope      = $this->getScope();
        $store      = Mage::app()->getRequest()->getParam('store');
        $website    = Mage::app()->getRequest()->getParam('website');

        if ($store && $website) {
            $path = "$scope/$store/$path";
            return $environmentConfigLoaderHelper->hasPath($path);
        }

        if ($website) {
            $path = "$scope/$website/$path";
            return $environmentConfigLoaderHelper->hasPath($path);
        }

        $path = "$scope/$path";
        return $environmentConfigLoaderHelper->hasPath($path);
    }

    /**
     * Retrieve label for scope
     *
     * @param  Mage_Core_Model_Config_Element $element
     * @return string
     */
    public function getScopeLabel($element)
    {
        if ((int) $element->show_in_store === 1) {
            return $this->_scopeLabels[self::SCOPE_STORES];
        }

        if ((int) $element->show_in_website === 1) {
            return $this->_scopeLabels[self::SCOPE_WEBSITES];
        }

        return $this->_scopeLabels[self::SCOPE_DEFAULT];
    }

    /**
     * Get current scope code
     *
     * @return string
     * @throws Exception
     */
    public function getScopeCode()
    {
        $scopeCode = $this->getData('scope_code');
        if (is_null($scopeCode)) {
            if ($this->getStoreCode()) {
                $scopeCode = $this->getStoreCode();
            } elseif ($this->getWebsiteCode()) {
                $scopeCode = $this->getWebsiteCode();
            } else {
                $scopeCode = '';
            }

            $this->setScopeCode($scopeCode);
        }

        return $scopeCode;
    }

    /**
     * Get current scope code
     *
     * @return int|string
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function getScopeId()
    {
        $scopeId = $this->getData('scope_id');
        if (is_null($scopeId)) {
            if ($this->getStoreCode()) {
                $scopeId = Mage::app()->getStore($this->getStoreCode())->getId();
            } elseif ($this->getWebsiteCode()) {
                $scopeId = Mage::app()->getWebsite($this->getWebsiteCode())->getId();
            } else {
                $scopeId = '';
            }

            $this->setScopeId($scopeId);
        }

        return $scopeId;
    }

    /**
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'export'        => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_export'),
            'import'        => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_import'),
            'allowspecific' => Mage::getConfig()
                ->getBlockClassName('adminhtml/system_config_form_field_select_allowspecific'),
            'image'         => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_image'),
            'file'          => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_file'),
            'logo'          => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_logo'),
        ];
    }

    /**
     * Temporary moved those $this->getRequest()->getParam('blabla') from the code across this block
     * to getBlala() methods to be later set from controller with setters
     */
    /**
     * @TODO delete this methods when {^see above^} is done
     * @return string
     * @throws Exception
     */
    public function getSectionCode()
    {
        return $this->getRequest()->getParam('section', '');
    }

    /**
     * @TODO delete this methods when {^see above^} is done
     * @return string
     * @throws Exception
     */
    public function getWebsiteCode()
    {
        return $this->getRequest()->getParam('website', '');
    }

    /**
     * @TODO delete this methods when {^see above^} is done
     * @return string
     * @throws Exception
     */
    public function getStoreCode()
    {
        return $this->getRequest()->getParam('store', '');
    }
}
