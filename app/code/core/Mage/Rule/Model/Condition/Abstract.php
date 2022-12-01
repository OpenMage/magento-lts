<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract Rule condition data model
 *
 * @category   Mage
 * @package    Mage_Rule
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method string|false getAttribute()
 * @method $this setAttribute(string|false $value)
 * @method array getAttributeOption()
 * @method bool getExplicitApply()
 * @method $this setJsFormObject(string  $value)
 * @method $this setIsValueParsed(string|false $value)
 * @method string|false getOperator()
 * @method $this setOperator(string|false $value)
 * @method array getOperatorByInputType()
 * @method $this setOperatorByInputType(array $value)
 * @method array getOperatorOption(string $value)
 * @method $this setOperatorOption(array $value)
 * @method array getOperatorOptions()
 * @method bool hasValueParsed()
 * @method $this setValueParsed(array $value)
 * @method string getPrefix()
 * @method Mage_Rule_Model_Abstract getRule()
 * @method string|false getType()
 * @method $this setType(string $value)
 * @method string|false getIsValueParsed()
 * @method $this setValue(string|false $value)
 * @method string getValueAfterElementHtml()
 * @method string getValueElementChooserUrl()
 * @method bool hasValueOption()
 * @method array getValueOption()
 * @method $this setValueOption(array $value)
 */
abstract class Mage_Rule_Model_Condition_Abstract extends Varien_Object implements Mage_Rule_Model_Condition_Interface
{
    /**
     * Defines which operators will be available for this condition
     *
     * @var string
     */
    protected $_inputType = null;

    /**
     * Default values for possible operator options
     * @var array
     */
    protected $_defaultOperatorOptions = null;

    /**
     * Default combinations of operator options, depending on input type
     * @var array
     */
    protected $_defaultOperatorInputByType = null;

    /**
     * List of input types for values which should be array
     * @var array
     */
    protected $_arrayInputTypes = [];

    public function __construct()
    {
        parent::__construct();

        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();

        if ($options = $this->getAttributeOptions()) {
            foreach ($options as $attr => $dummy) {
                $this->setAttribute($attr);
                break;
            }
        }
        if ($options = $this->getOperatorOptions()) {
            foreach ($options as $operator => $dummy) {
                $this->setOperator($operator);
                break;
            }
        }
    }

    /**
     * Prepare sql where by condition
     *
     * @return string
     */
    public function prepareConditionSql()
    {
        return '';
    }

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if ($this->_defaultOperatorInputByType === null) {
            $this->_defaultOperatorInputByType = [
                'string'      => ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'],
                'numeric'     => ['==', '!=', '>=', '>', '<=', '<', '()', '!()'],
                'date'        => ['==', '>=', '<='],
                'datetime'    => ['==', '>=', '<='],
                'select'      => ['==', '!='],
                'boolean'     => ['==', '!='],
                'multiselect' => ['[]', '![]', '()', '!()'],
                'grid'        => ['()', '!()'],
            ];
            $this->_arrayInputTypes = ['multiselect', 'grid'];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Default operator options getter
     * Provides all possible operator options
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        if ($this->_defaultOperatorOptions === null) {
            $this->_defaultOperatorOptions = [
                '=='  => Mage::helper('rule')->__('is'),
                '!='  => Mage::helper('rule')->__('is not'),
                '>='  => Mage::helper('rule')->__('equals or greater than'),
                '<='  => Mage::helper('rule')->__('equals or less than'),
                '>'   => Mage::helper('rule')->__('greater than'),
                '<'   => Mage::helper('rule')->__('less than'),
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),
                '[]'  => Mage::helper('rule')->__('contains'),
                '![]' => Mage::helper('rule')->__('does not contain'),
                '()'  => Mage::helper('rule')->__('is one of'),
                '!()' => Mage::helper('rule')->__('is not one of')
            ];
        }
        return $this->_defaultOperatorOptions;
    }

    /**
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->getRule()->getForm();
    }

    /**
     * @param array $arrAttributes
     * @return array
     */
    public function asArray(array $arrAttributes = [])
    {
        return [
            'type'               => $this->getType(),
            'attribute'          => $this->getAttribute(),
            'operator'           => $this->getOperator(),
            'value'              => $this->getValue(),
            'is_value_processed' => $this->getIsValueParsed(),
        ];
    }

    /**
     * @return string
     */
    public function asXml()
    {
        return '<type>' . $this->getType() . '</type>'
            . '<attribute>' . $this->getAttribute() . '</attribute>'
            . '<operator>' . $this->getOperator() . '</operator>'
            . '<value>' . $this->getValue() . '</value>';
    }

    /**
     * @param array $arr
     * @return $this
     */
    public function loadArray($arr)
    {
        $this->setType($arr['type']);
        $this->setAttribute($arr['attribute'] ?? false);
        $this->setOperator($arr['operator'] ?? false);
        $this->setValue($arr['value'] ?? false);
        $this->setIsValueParsed($arr['is_value_parsed'] ?? false);

        return $this;
    }

    /**
     * @param SimpleXMLElement|string $xml
     * @return $this
     */
    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = (array)$xml;
        $this->loadArray($arr);
        return $this;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributeOptions()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAttributeSelectOptions()
    {
        $opt = [];
        foreach ($this->getAttributeOption() as $k => $v) {
            $opt[] = ['value' => $k, 'label' => $v];
        }
        return $opt;
    }

    /**
     * @return array
     */
    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }

    /**
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption($this->getDefaultOperatorOptions());
        $this->setOperatorByInputType($this->getDefaultOperatorInputByType());
        return $this;
    }

    /**
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid, bool
     *
     * @return string
     */
    public function getInputType()
    {
        return $this->_inputType ?? 'string';
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        $type = $this->getInputType();
        $opt = [];
        $operatorByType = $this->getOperatorByInputType();
        foreach ($this->getOperatorOption() as $k => $v) {
            if (!$operatorByType || in_array($k, $operatorByType[$type])) {
                $opt[] = ['value' => $k, 'label' => $v];
            }
        }
        return $opt;
    }

    /**
     * @return array
     */
    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }

    /**
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption([]);
        return $this;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        $valueOption = $opt = [];
        if ($this->hasValueOption()) {
            $valueOption = (array) $this->getValueOption();
        }
        foreach ($valueOption as $k => $v) {
            $opt[] = ['value' => $k, 'label' => $v];
        }
        return $opt;
    }

    /**
     * Retrieve parsed value
     *
     * @return array|string|int|float
     */
    public function getValueParsed()
    {
        if (!$this->hasValueParsed()) {
            $value = $this->getData('value');
            if ($this->isArrayOperatorType() && is_string($value)) {
                $value = preg_split('#\s*[,;]\s*#', $value, -1, PREG_SPLIT_NO_EMPTY);
            }
            $this->setValueParsed($value);
        }
        return $this->getData('value_parsed');
    }

    /**
     * Check if value should be array
     *
     * Depends on operator input type
     *
     * @return bool
     */
    public function isArrayOperatorType()
    {
        $op = $this->getOperator();
        return $op === '()' || $op === '!()' || in_array($this->getInputType(), $this->_arrayInputTypes);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        if (!$this->getIsValueParsed()) {
            // date format intentionally hard-coded
            $format = null;
            switch ($this->getInputType()) {
                case 'date':
                    $format = Varien_Date::DATE_INTERNAL_FORMAT;
                    break;

                case 'datetime':
                    $format = Varien_Date::DATETIME_INTERNAL_FORMAT;
                    break;
            }

            if ($format !== null) {
                $this->setValue(
                    Mage::app()->getLocale()->date(
                        $this->getData('value'),
                        $format,
                        null,
                        false
                    )->toString($format)
                );
                $this->setIsValueParsed(true);
            }
        }
        return $this->getData('value');
    }

    /**
     * @return string
     */
    public function getValueName()
    {
        $value = $this->getValue();
        if (is_null($value) || $value === '') {
            return '...';
        }

        $options = $this->getValueSelectOptions();
        $valueArr = [];
        if (!empty($options)) {
            foreach ($options as $o) {
                if (is_array($value)) {
                    if (in_array($o['value'], $value)) {
                        $valueArr[] = $o['label'];
                    }
                } else {
                    if (is_array($o['value'])) {
                        foreach ($o['value'] as $v) {
                            if ($v['value'] == $value) {
                                return $v['label'];
                            }
                        }
                    }
                    if ($o['value'] == $value) {
                        return $o['label'];
                    }
                }
            }
        }
        if (!empty($valueArr)) {
            $value = implode(', ', $valueArr);
        }
        return $value;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [
            ['value' => '', 'label' => Mage::helper('rule')->__('Please choose a condition to add...')],
        ];
    }

    /**
     * @return string
     */
    public function getNewChildName()
    {
        return $this->getAddLinkHtml();
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
           . $this->getAttributeElementHtml()
           . $this->getOperatorElementHtml()
           . $this->getValueElementHtml()
           . $this->getRemoveLinkHtml()
           . $this->getChooserContainerHtml();
    }

    /**
     * @return string
     */
    public function asHtmlRecursive()
    {
        return $this->asHtml();
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getTypeElement()
    {
        return $this->getForm()->addField($this->getPrefix() . '__' . $this->getId() . '__type', 'hidden', [
            'name'    => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][type]',
            'value'   => $this->getType(),
            'no_span' => true,
            'class'   => 'hidden',
        ]);
    }

    /**
     * @return string
     */
    public function getTypeElementHtml()
    {
        return $this->getTypeElement()->getHtml();
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        if (is_null($this->getAttribute())) {
            foreach ($this->getAttributeOption() as $k => $v) {
                $this->setAttribute($k);
                break;
            }
        }
        return $this->getForm()->addField($this->getPrefix() . '__' . $this->getId() . '__attribute', 'select', [
            'name'       => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][attribute]',
            'values'     => $this->getAttributeSelectOptions(),
            'value'      => $this->getAttribute(),
            'value_name' => $this->getAttributeName(),
        ])->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    /**
     * @return string
     */
    public function getAttributeElementHtml()
    {
        return $this->getAttributeElement()->getHtml();
    }

    /**
     * Retrieve Condition Operator element Instance
     * If the operator value is empty - define first available operator value as default
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getOperatorElement()
    {
        $options = $this->getOperatorSelectOptions();
        if (is_null($this->getOperator())) {
            foreach ($options as $option) {
                $this->setOperator($option['value']);
                break;
            }
        }

        $elementId   = sprintf('%s__%s__operator', $this->getPrefix(), $this->getId());
        $elementName = sprintf('rule[%s][%s][operator]', $this->getPrefix(), $this->getId());
        $element     = $this->getForm()->addField($elementId, 'select', [
            'name'          => $elementName,
            'values'        => $options,
            'value'         => $this->getOperator(),
            'value_name'    => $this->getOperatorName(),
        ]);
        $element->setRenderer(Mage::getBlockSingleton('rule/editable'));

        return $element;
    }

    /**
     * @return string
     */
    public function getOperatorElementHtml()
    {
        return $this->getOperatorElement()->getHtml();
    }

    /**
     * Value element type will define renderer for condition value element
     *
     * @see Varien_Data_Form_Element
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return false|Mage_Rule_Block_Editable|object
     */
    public function getValueElementRenderer()
    {
        if (strpos($this->getValueElementType(), '/') !== false) {
            return Mage::getBlockSingleton($this->getValueElementType());
        }
        return Mage::getBlockSingleton('rule/editable');
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement()
    {
        $elementParams = [
            'name'               => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][value]',
            'value'              => $this->getValue(),
            'values'             => $this->getValueSelectOptions(),
            'value_name'         => $this->getValueName(),
            'after_element_html' => $this->getValueAfterElementHtml(),
            'explicit_apply'     => $this->getExplicitApply(),
        ];

        switch ($this->getInputType()) {
            case 'date':
                $elementParams['input_format'] = Varien_Date::DATE_INTERNAL_FORMAT;
                $elementParams['format']       = Varien_Date::DATE_INTERNAL_FORMAT;
                break;

            case 'datetime':
                $elementParams['input_format'] = Varien_Date::DATETIME_INTERNAL_FORMAT;
                $elementParams['format']       = Varien_Date::DATETIME_INTERNAL_FORMAT;
                $elementParams['time']         = true;
                break;
        }

        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__value',
            $this->getValueElementType(),
            $elementParams
        )->setRenderer($this->getValueElementRenderer());
    }

    /**
     * @return string
     */
    public function getValueElementHtml()
    {
        return $this->getValueElement()->getHtml();
    }

    /**
     * @return string
     */
    public function getAddLinkHtml()
    {
        $src = Mage::getDesign()->getSkinUrl('images/rule_component_add.gif');
        return '<img src="' . $src . '" class="rule-param-add v-middle" alt="" title="'
            . Mage::helper('core')->quoteEscape(Mage::helper('rule')->__('Add'))
            . '"/>';
    }

    /**
     * @return string
     */
    public function getRemoveLinkHtml()
    {
        $src = Mage::getDesign()->getSkinUrl('images/rule_component_remove.gif');
        return ' <span class="rule-param"><a href="javascript:void(0)" class="rule-param-remove" title="'
            . Mage::helper('core')->quoteEscape(Mage::helper('rule')->__('Remove'))
            . '"><img src="' . $src . '"  alt="" class="v-middle" /></a></span>';
    }

    /**
     * @return string
     */
    public function getChooserContainerHtml()
    {
        $url = $this->getValueElementChooserUrl();
        $html = '';
        if ($url) {
            $html = '<div class="rule-chooser" url="' . $url . '"></div>';
        }
        return $html;
    }

    /**
     * @param string $format
     * @return string
     */
    public function asString($format = '')
    {
        return $this->getAttributeName() . ' ' . $this->getOperatorName() . ' ' . $this->getValueName();
    }

    /**
     * @param int $level
     * @return string
     */
    public function asStringRecursive($level = 0)
    {
        return str_pad('', $level * 3, ' ', STR_PAD_LEFT) . $this->asString();
    }

    /**
     * Validate product attribute value for condition
     *
     * @param   mixed $validatedValue product attribute value
     * @return  bool
     */
    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        /**
         * Condition attribute value
         */
        $value = $this->getValueParsed();

        /**
         * Comparison operator
         */
        $op = $this->getOperatorForValidate();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;

        switch ($op) {
            case '==':
            case '!=':
                if (is_array($value)) {
                    if (is_array($validatedValue)) {
                        $result = array_intersect($value, $validatedValue);
                        $result = !empty($result);
                    } else {
                        return false;
                    }
                } else {
                    if (is_array($validatedValue)) {
                        $result = count($validatedValue) == 1 && array_shift($validatedValue) == $value;
                    } else {
                        $result = $this->_compareValues($validatedValue, $value);
                    }
                }
                break;

            case '<=':
            case '>':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = $validatedValue <= $value;
                }
                break;

            case '>=':
            case '<':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = $validatedValue >= $value;
                }
                break;

            case '{}':
            case '!{}':
                if (is_scalar($validatedValue) && is_array($value)) {
                    foreach ($value as $item) {
                        if (stripos($validatedValue, $item) !== false) {
                            $result = true;
                            break;
                        }
                    }
                } elseif (is_array($value)) {
                    if (is_array($validatedValue)) {
                        $result = array_intersect($value, $validatedValue);
                        $result = !empty($result);
                    } else {
                        return false;
                    }
                } else {
                    if (is_array($validatedValue)) {
                        $result = in_array($value, $validatedValue);
                    } else {
                        $result = $this->_compareValues($value, $validatedValue, false);
                    }
                }
                break;

            case '()':
            case '!()':
            case '[]':
            case '![]':
                if (is_array($validatedValue)) {
                    $value = (array)$value;
                    $match = count(array_intersect($validatedValue, $value));

                    if (in_array($op, ['[]', '![]'])) {
                        $result = $match == count($value);
                    } else {
                        $result = $match > 0;
                    }
                } else {
                    $value = (array)$value;
                    foreach ($value as $item) {
                        if ($this->_compareValues($validatedValue, $item)) {
                            $result = true;
                            break;
                        }
                    }
                }
                break;
        }

        if ($op == '!=' || $op == '>' || $op == '<' || $op == '!{}' || $op == '!()' || $op == '![]') {
            $result = !$result;
        }

        return $result;
    }

    /**
     * Case and type insensitive comparison of values
     *
     * @param string|int|float $validatedValue
     * @param string|int|float $value
     * @param bool $strict
     * @return bool
     */
    protected function _compareValues($validatedValue, $value, $strict = true)
    {
        if ($strict && is_numeric($validatedValue) && is_numeric($value)) {
            return $validatedValue == $value;
        } else {
            $validatePattern = preg_quote($validatedValue, '~');
            if ($strict) {
                $validatePattern = '^' . $validatePattern . '$';
            }
            return (bool)preg_match('~' . $validatePattern . '~iu', $value);
        }
    }

    /**
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        return $this->validateAttribute($object->getData($this->getAttribute()));
    }

    /**
     * Retrieve operator for php validation
     *
     * @return string
     */
    public function getOperatorForValidate()
    {
        return $this->getOperator();
    }
}
