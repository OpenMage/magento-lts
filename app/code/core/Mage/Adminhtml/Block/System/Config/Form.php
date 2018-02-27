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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * System config form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Form extends Mage_Adminhtml_Block_Widget_Form
{

    const SCOPE_DEFAULT = 'default';
    const SCOPE_WEBSITES = 'websites';
    const SCOPE_STORES   = 'stores';

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
     * Enter description here...
     *
     * @var Varien_Simplexml_Element
     */
    protected $_configRoot;

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Model_Config
     */
    protected $_configFields;

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Block_System_Config_Form_Fieldset
     */
    protected $_defaultFieldsetRenderer;

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected $_defaultFieldRenderer;

    /**
     * Enter description here...
     *
     * @var array
     */
    protected $_fieldsets = array();

    /**
     * Translated scope labels
     *
     * @var array
     */
    protected $_scopeLabels = array();

    /**
     * Enter description here...
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_scopeLabels = array(
            self::SCOPE_DEFAULT  => Mage::helper('adminhtml')->__('[GLOBAL]'),
            self::SCOPE_WEBSITES => Mage::helper('adminhtml')->__('[WEBSITE]'),
            self::SCOPE_STORES   => Mage::helper('adminhtml')->__('[STORE VIEW]'),
        );
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    protected function _initObjects()
    {
        /** @var $_configDataObject Mage_Adminhtml_Model_Config_Data */
        $this->_configDataObject = Mage::getSingleton('adminhtml/config_data');
        $this->_configRoot = $this->_configDataObject->getConfigRoot();
        $this->_configData = $this->_configDataObject->load();

        $this->_configFields = Mage::getSingleton('adminhtml/config');

        $this->_defaultFieldsetRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_fieldset');
        $this->_defaultFieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    public function initForm()
    {
        $this->_initObjects();

        $form = new Varien_Data_Form();

        $sections = $this->_configFields->getSection(
            $this->getSectionCode(),
            $this->getWebsiteCode(),
            $this->getStoreCode()
        );
        if (empty($sections)) {
            $sections = array();
        }
        foreach ($sections as $section) {
            /* @var $section Varien_Simplexml_Element */
            if (!$this->_canShowField($section)) {
                continue;
            }
            foreach ($section->groups as $groups){
                $groups = (array)$groups;
                usort($groups, array($this, '_sortForm'));

                foreach ($groups as $group){
                    /* @var $group Varien_Simplexml_Element */
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
     * @param Varien_Data_Form $form
     * @param Varien_Simplexml_Element $group
     * @param Varien_Simplexml_Element $section
     * @param Varien_Data_Form_Element_Fieldset|null $parentElement
     */
    protected function _initGroup($form, $group, $section, $parentElement = null)
    {
        if ($group->frontend_model) {
            $fieldsetRenderer = Mage::getBlockSingleton((string)$group->frontend_model);
        } else {
            $fieldsetRenderer = $this->_defaultFieldsetRenderer;
        }

        $fieldsetRenderer->setForm($this)
            ->setConfigData($this->_configData);

        if ($this->_configFields->hasChildren($group, $this->getWebsiteCode(), $this->getStoreCode())) {
            $helperName = $this->_configFields->getAttributeModule($section, $group);
            $fieldsetConfig = array('legend' => Mage::helper($helperName)->__((string)$group->label));
            if (!empty($group->comment)) {
                $fieldsetConfig['comment'] = Mage::helper($helperName)->__((string)$group->comment);
            }
            if (!empty($group->expanded)) {
                $fieldsetConfig['expanded'] = (bool)$group->expanded;
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
                    $cloneModel = Mage::getModel((string)$group->clone_model);
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
        if (!$this->getChild('element_dependense')){
            $this->setChild('element_dependense',
                $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));
        }
        return $this->getChild('element_dependense');
    }

    /**
     * Init fieldset fields
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param Varien_Simplexml_Element $group
     * @param Varien_Simplexml_Element $section
     * @param string $fieldPrefix
     * @param string $labelPrefix
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    public function initFields($fieldset, $group, $section, $fieldPrefix='', $labelPrefix='')
    {
        if (!$this->_configDataObject) {
            $this->_initObjects();
        }

        // Extends for config data
        $configDataAdditionalGroups = array();

        foreach ($group->fields as $elements) {
            $elements = (array)$elements;
            // sort either by sort_order or by child node values bypassing the sort_order
            if ($group->sort_fields && $group->sort_fields->by) {
                $fieldset->setSortElementsByAttribute(
                    (string)$group->sort_fields->by,
                    $group->sort_fields->direction_desc ? SORT_DESC : SORT_ASC
                );
            } else {
                usort($elements, array($this, '_sortForm'));
            }

            foreach ($elements as $element) {
                if (!$this->_canShowField($element)) {
                    continue;
                }

                if ((string)$element->getAttribute('type') == 'group') {
                    $this->_initGroup($fieldset->getForm(), $element, $section, $fieldset);
                    continue;
                }

                /**
                 * Look for custom defined field path
                 */
                $path = (string)$element->config_path;
                if (empty($path)) {
                    $path = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $element->getName();
                } elseif (strrpos($path, '/') > 0) {
                    // Extend config data with new section group
                    $groupPath = substr($path, 0, strrpos($path, '/'));
                    if (!isset($configDataAdditionalGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject->extendConfig(
                            $groupPath,
                            false,
                            $this->_configData
                        );
                        $configDataAdditionalGroups[$groupPath] = true;
                    }
                }

                $data = $this->_configDataObject->getConfigDataValue($path, $inherit, $this->_configData);
                if ($element->frontend_model) {
                    $fieldRenderer = Mage::getBlockSingleton((string)$element->frontend_model);
                } else {
                    $fieldRenderer = $this->_defaultFieldRenderer;
                }

                $fieldRenderer->setForm($this);
                $fieldRenderer->setConfigData($this->_configData);

                $helperName = $this->_configFields->getAttributeModule($section, $group, $element);
                $fieldType  = (string)$element->frontend_type ? (string)$element->frontend_type : 'text';
                $name  = 'groups[' . $group->getName() . '][fields][' . $fieldPrefix.$element->getName() . '][value]';
                $label =  Mage::helper($helperName)->__($labelPrefix) . ' '
                    . Mage::helper($helperName)->__((string)$element->label);
                $hint  = (string)$element->hint ? Mage::helper($helperName)->__((string)$element->hint) : '';

                if ($element->backend_model) {
                    $model = Mage::getModel((string)$element->backend_model);
                    if (!$model instanceof Mage_Core_Model_Config_Data) {
                        Mage::throwException('Invalid config field backend model: '.(string)$element->backend_model);
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
                        /* @var $dependent Mage_Core_Model_Config_Element */

                        if (isset($dependent->fieldset)) {
                            $dependentFieldGroupName = (string)$dependent->fieldset;
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
                        $dependentValue = (string)(isset($dependent->value) ? $dependent->value : $dependent);
                        if (isset($dependent['separator'])) {
                            $dependentValue = explode((string)$dependent['separator'], $dependentValue);
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

                $field = $fieldset->addField($id, $fieldType, array(
                    'name'                  => $name,
                    'label'                 => $label,
                    'comment'               => $comment,
                    'tooltip'               => $tooltip,
                    'hint'                  => $hint,
                    'value'                 => $data,
                    'inherit'               => $inherit,
                    'class'                 => $element->frontend_class . $sharedClass . $requiresClass,
                    'field_config'          => $element,
                    'scope'                 => $this->getScope(),
                    'scope_id'              => $this->getScopeId(),
                    'scope_label'           => $this->getScopeLabel($element),
                    'can_use_default_value' => $this->canUseDefaultValue((int)$element->show_in_default),
                    'can_use_website_value' => $this->canUseWebsiteValue((int)$element->show_in_website),
                ));
                $this->_prepareFieldOriginalData($field, $element);

                if (isset($element->validate)) {
                    $field->addClass($element->validate);
                }

                if (isset($element->frontend_type)
                    && 'multiselect' === (string)$element->frontend_type
                    && isset($element->can_be_empty)
                ) {
                    $field->setCanBeEmpty(true);
                }

                $field->setRenderer($fieldRenderer);

                if ($element->source_model) {
                    // determine callback for the source model
                    $factoryName = (string)$element->source_model;
                    $method = false;
                    if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
                        array_shift($matches);
                        list($factoryName, $method) = array_values($matches);
                    }

                    $sourceModel = Mage::getSingleton($factoryName);
                    if ($sourceModel instanceof Varien_Object) {
                        $sourceModel->setPath($path);
                    }
                    if ($method) {
                        if ($fieldType == 'multiselect') {
                            $optionArray = $sourceModel->$method();
                        } else {
                            $optionArray = array();
                            foreach ($sourceModel->$method() as $value => $label) {
                                $optionArray[] = array('label' => $label, 'value' => $value);
                            }
                        }
                    } else {
                        $optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
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
     * @param Varien_Simplexml_Element $xmlElement
     */
    protected function _prepareFieldOriginalData($field, $xmlElement)
    {
        $originalData = array();
        foreach ($xmlElement as $key => $value) {
            if (!$value->hasChildren()) {
                $originalData[$key] = (string)$value;
            }
        }
        $field->setOriginalData($originalData);
    }

    /**
     * Support models "getCommentText" method for field note generation
     *
     * @param Mage_Core_Model_Config_Element $element
     * @param string $helper
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
     * Prepare additional comment for field like tooltip
     *
     * @param Mage_Core_Model_Config_Element $element
     * @param string $helper
     * @return string
     */
    protected function _prepareFieldTooltip($element, $helper)
    {
        if ($element->tooltip) {
            return Mage::helper($helper)->__((string)$element->tooltip);
        } elseif ($element->tooltip_block) {
            return $this->getLayout()->createBlock((string)$element->tooltip_block)->toHtml();
        }
        return '';
    }

    /**
     * Append dependence block at then end of form block
     *
     *
     */
    protected function _afterToHtml($html)
    {
        if ($this->_getDependence()) {
            $html .= $this->_getDependence()->toHtml();
        }
        $html = parent::_afterToHtml($html);
        return $html;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Simplexml_Element $a
     * @param Varien_Simplexml_Element $b
     * @return boolean
     */
    protected function _sortForm($a, $b)
    {
        return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);

    }

    /**
     * Enter description here...
     *
     * @param Varien_Simplexml_Element $field
     * @return boolean
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
     * Enter description here...
     *
     * @param Varien_Simplexml_Element $field
     * @return boolean
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
     * @param   Varien_Simplexml_Element $field
     * @return  bool
     */
    protected function _canShowField($field)
    {
        $ifModuleEnabled = trim((string)$field->if_module_enabled);
        if ($ifModuleEnabled && !Mage::helper('Core')->isModuleEnabled($ifModuleEnabled)) {
            return false;
        }

        switch ($this->getScope()) {
            case self::SCOPE_DEFAULT:
                return (int)$field->show_in_default;
                break;
            case self::SCOPE_WEBSITES:
                return (int)$field->show_in_website;
                break;
            case self::SCOPE_STORES:
                return (int)$field->show_in_store;
                break;
        }
        return true;
    }

    /**
     * Retrieve current scope
     *
     * @return string
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
     * Retrieve label for scope
     *
     * @param Mage_Core_Model_Config_Element $element
     * @return string
     */
    public function getScopeLabel($element)
    {
        if ($element->show_in_store == 1) {
            return $this->_scopeLabels[self::SCOPE_STORES];
        } elseif ($element->show_in_website == 1) {
            return $this->_scopeLabels[self::SCOPE_WEBSITES];
        }
        return $this->_scopeLabels[self::SCOPE_DEFAULT];
    }

    /**
     * Get current scope code
     *
     * @return string
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
     * Enter description here...
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'export'        => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_export'),
            'import'        => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_import'),
            'allowspecific' => Mage::getConfig()
                ->getBlockClassName('adminhtml/system_config_form_field_select_allowspecific'),
            'image'         => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_image'),
            'file'          => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_file')
        );
    }

    /**
     * Temporary moved those $this->getRequest()->getParam('blabla') from the code accross this block
     * to getBlala() methods to be later set from controller with setters
     */
    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getSectionCode()
    {
        return $this->getRequest()->getParam('section', '');
    }

    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->getRequest()->getParam('website', '');
    }

    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getStoreCode()
    {
        return $this->getRequest()->getParam('store', '');
    }

}
