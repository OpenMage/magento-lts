<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rule
 */

/**
 * Abstract Rule condition data model
 *
 * @package    Mage_Rule
 *
 * @method false|string             getAttribute()
 * @method array|string             getAttributeOption()
 * @method bool                     getExplicitApply()
 * @method false|string             getIsValueParsed()
 * @method false|string             getOperator()
 * @method array                    getOperatorByInputType()
 * @method array|string             getOperatorOption()
 * @method array                    getOperatorOptions()
 * @method string                   getPrefix()
 * @method Mage_Rule_Model_Abstract getRule()
 * @method false|string             getType()
 * @method string                   getValueAfterElementHtml()
 * @method string                   getValueElementChooserUrl()
 * @method array                    getValueOption()
 * @method bool                     hasValueOption()
 * @method bool                     hasValueParsed()
 * @method $this                    setAttribute(false|string $value)
 * @method $this                    setIsValueParsed(false|string $value)
 * @method $this                    setJsFormObject(string  $value)
 * @method $this                    setOperator(false|string $value)
 * @method $this                    setOperatorByInputType(array $value)
 * @method $this                    setOperatorOption(array $value)
 * @method $this                    setType(string $value)
 * @method $this                    setValue(false|string $value)
 * @method $this                    setValueOption(array $value)
 * @method $this                    setValueParsed(array $value)
 */
abstract class Mage_Rule_Model_Condition_Abstract extends Varien_Object implements Mage_Rule_Model_Condition_Interface
{
    /**
     * Flag to enable translation for loadOperatorOptions/loadValueOptions/loadAggregatorOptions/getDefaultOperatorOptions
     * It's useless to translate these data on frontend
     *
     * @var bool
     */
    protected static $translate;

    /**
     * Defines which operators will be available for this condition
     *
     * @var null|string
     */
    protected $_inputType = null;

    /**
     * Default values for possible operator options
     *
     * @var null|array
     */
    protected $_defaultOperatorOptions = null;

    /**
     * Default combinations of operator options, depending on input type
     *
     * @var null|array
     */
    protected $_defaultOperatorInputByType = null;

    /**
     * List of input types for values which should be array
     * @var array
     */
    protected $_arrayInputTypes = [];

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct()
    {
        if (!is_bool(static::$translate)) {
            static::$translate = Mage::app()->getStore()->isAdmin();
        }

        parent::__construct();

        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();

        if ($options = $this->getAttributeOptions()) {
            foreach (array_keys($options) as $attr) {
                $this->setAttribute($attr);
                break;
            }
        }

        if ($options = $this->getOperatorOptions()) {
            foreach (array_keys($options) as $operator) {
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
                '=='  => static::$translate ? Mage::helper('rule')->__('is') : 'is',
                '!='  => static::$translate ? Mage::helper('rule')->__('is not') : 'is not',
                '>='  => static::$translate ? Mage::helper('rule')->__('equals or greater than') : 'equals or greater than',
                '<='  => static::$translate ? Mage::helper('rule')->__('equals or less than') : 'equals or less than',
                '>'   => static::$translate ? Mage::helper('rule')->__('greater than') : 'greater than',
                '<'   => static::$translate ? Mage::helper('rule')->__('less than') : 'less than',
                '{}'  => static::$translate ? Mage::helper('rule')->__('contains') : 'contains',
                '!{}' => static::$translate ? Mage::helper('rule')->__('does not contain') : 'does not contain',
                '[]'  => static::$translate ? Mage::helper('rule')->__('contains') : 'contains',
                '![]' => static::$translate ? Mage::helper('rule')->__('does not contain') : 'does not contain',
                '()'  => static::$translate ? Mage::helper('rule')->__('is one of') : 'is one of',
                '!()' => static::$translate ? Mage::helper('rule')->__('is not one of') : 'is not one of',
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
     * @param  array|Mage_Rule_Model_Condition_Abstract $arr
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
     * @param  SimpleXMLElement|string $xml
     * @return $this
     */
    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }

        $arr = (array) $xml;
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
        foreach ($this->getAttributeOption() as $key => $value) {
            $opt[] = ['value' => $key, 'label' => $value];
        }

        return $opt;
    }

    /**
     * @return string
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
        foreach ($this->getOperatorOption() as $key => $value) {
            if (!$operatorByType || in_array($key, $operatorByType[$type])) {
                $opt[] = ['value' => $key, 'label' => $value];
            }
        }

        return $opt;
    }

    /**
     * @return string
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
        $valueOption = [];
        $opt = [];
        if ($this->hasValueOption()) {
            $valueOption = (array) $this->getValueOption();
        }

        foreach ($valueOption as $key => $value) {
            $opt[] = ['value' => $key, 'label' => $value];
        }

        return $opt;
    }

    /**
     * Retrieve parsed value
     *
     * @return array|float|int|string
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
        $operator = $this->getOperator();
        return $operator === '()' || $operator === '!()' || in_array($this->getInputType(), $this->_arrayInputTypes);
    }

    /**
     * @return null|array|int|string
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
                        false,
                    )->toString($format),
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
            foreach ($options as $option) {
                if (is_array($value)) {
                    if (in_array($option['value'], $value)) {
                        $valueArr[] = $option['label'];
                    }
                } else {
                    if (is_array($option['value'])) {
                        foreach ($option['value'] as $optionValue) {
                            if ($optionValue['value'] == $value) {
                                return $optionValue['label'];
                            }
                        }
                    }

                    if ($option['value'] == $value) {
                        return $option['label'];
                    }
                }
            }
        }

        if (!empty($valueArr)) {
            return implode(', ', $valueArr);
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
     * @throws Exception
     */
    public function getNewChildName()
    {
        return $this->getAddLinkHtml();
    }

    /**
     * @return string
     * @throws Exception
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
     * @throws Exception
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
            foreach (array_keys($this->getAttributeOption()) as $key) {
                $this->setAttribute($key);
                break;
            }
        }

        $element = $this->getForm()->addField($this->getPrefix() . '__' . $this->getId() . '__attribute', 'select', [
            'name'       => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][attribute]',
            'values'     => $this->getAttributeSelectOptions(),
            'value'      => $this->getAttribute(),
            'value_name' => $this->getAttributeName(),
        ]);

        $renderer = Mage::getBlockSingleton('rule/editable');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $element->setRenderer($renderer);
        }

        return $element;
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

        $renderer = Mage::getBlockSingleton('rule/editable');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $element->setRenderer($renderer);
        }

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
     * @return string
     * @see Varien_Data_Form_Element
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
        if (str_contains($this->getValueElementType(), '/')) {
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
            $elementParams,
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
     * @throws Exception
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
     * @throws Exception
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
            return '<div class="rule-chooser" url="' . $url . '"></div>';
        }

        return $html;
    }

    /**
     * @param  string $format
     * @return string
     */
    public function asString($format = '')
    {
        return $this->getAttributeName() . ' ' . $this->getOperatorName() . ' ' . $this->getValueName();
    }

    /**
     * @param  int    $level
     * @return string
     */
    public function asStringRecursive($level = 0)
    {
        return str_pad('', $level * 3, ' ', STR_PAD_LEFT) . $this->asString();
    }

    /**
     * Validate product attribute value for condition
     *
     * @param  mixed $validatedValue product attribute value
     * @return bool
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
        $operator = $this->getOperatorForValidate();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;

        switch ($operator) {
            case '==':
            case '!=':
                if (is_array($value)) {
                    if (is_array($validatedValue)) {
                        $result = array_intersect($value, $validatedValue);
                        $result = !empty($result);
                    } else {
                        return false;
                    }
                } elseif (is_array($validatedValue)) {
                    $result = count($validatedValue) == 1 && array_shift($validatedValue) == $value;
                } else {
                    $result = $this->_compareValues($validatedValue, $value);
                }

                break;

            case '<=':
            case '>':
                if (!is_scalar($validatedValue)) {
                    return false;
                }

                $result = $validatedValue <= $value;

                break;

            case '>=':
            case '<':
                if (!is_scalar($validatedValue)) {
                    return false;
                }

                $result = $validatedValue >= $value;

                break;

            case '{}':
            case '!{}':
                if (is_scalar($validatedValue) && is_array($value)) {
                    foreach ($value as $item) {
                        if (stripos($validatedValue, (string) $item) !== false) {
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
                } elseif (is_array($validatedValue)) {
                    $result = in_array($value, $validatedValue);
                } else {
                    $result = $this->_compareValues($value, $validatedValue, false);
                }

                break;

            case '()':
            case '!()':
            case '[]':
            case '![]':
                if (is_array($validatedValue)) {
                    $value = (array) $value;
                    $match = count(array_intersect($validatedValue, $value));

                    if (in_array($operator, ['[]', '![]'])) {
                        $result = $match == count($value);
                    } else {
                        $result = $match > 0;
                    }
                } else {
                    $value = (array) $value;
                    foreach ($value as $item) {
                        if ($this->_compareValues($validatedValue, $item)) {
                            $result = true;
                            break;
                        }
                    }
                }

                break;
        }

        if (in_array($operator, ['!=', '>', '<', '!{}', '!()', '![]'])) {
            return !$result;
        }

        return $result;
    }

    /**
     * Case and type insensitive comparison of values
     *
     * @param  float|int|string $validatedValue
     * @param  float|int|string $value
     * @param  bool             $strict
     * @return bool
     */
    protected function _compareValues($validatedValue, $value, $strict = true)
    {
        if ($strict && is_numeric($validatedValue) && is_numeric($value)) {
            return $validatedValue == $value;
        }

        $validatedValue = $validatedValue ?? '';
        $validatePattern = preg_quote($validatedValue, '~');
        if ($strict) {
            $validatePattern = '^' . $validatePattern . '$';
        }

        return (bool) preg_match('~' . $validatePattern . '~iu', $value);
    }

    /**
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
