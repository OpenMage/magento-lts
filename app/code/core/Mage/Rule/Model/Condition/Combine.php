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
 * @package     Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @method $this setActions(array $value)
 * @method string getAggregator()
 * @method $this setAggregator(string $value)
 * @method string getAggregatorOption()
 * @method array getAggregatorOptions()
 * @method $this setAggregatorOption(array $value)
 * @method string getPrefix()
 * @method $this setValueOption(array $value)
 */
class Mage_Rule_Model_Condition_Combine extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Store all used condition models
     *
     * @var array
     */
    static protected $_conditionModels = array();

    /**
     * Prepare sql where by condition
     *
     * @return string
     */
    public function prepareConditionSql()
    {
        $wheres = array();
        foreach ($this->getConditions() as $condition) {
            /** @var Mage_Rule_Model_Condition_Abstract $condition */
            $wheres[] = $condition->prepareConditionSql();
        }

        if (empty($wheres)) {
            return '';
        }
        $delimiter = $this->getAggregator() == "all" ? ' AND ' : ' OR ';
        return ' (' . implode($delimiter, $wheres) . ') ';
    }

    /**
     * Retrieve new object for each requested model.
     * If model is requested first time, store it at static array.
     *
     * It's made by performance reasons to avoid initialization of same models each time when rules are being processed.
     *
     * @param  string $modelClass
     * @return Mage_Rule_Model_Condition_Abstract|bool
     */
    protected function _getNewConditionModelInstance($modelClass)
    {
        if (empty($modelClass)) {
            return false;
        }

        if (!array_key_exists($modelClass, self::$_conditionModels)) {
            $model = Mage::getModel($modelClass);
            self::$_conditionModels[$modelClass] = $model;
        } else {
            $model = self::$_conditionModels[$modelClass];
        }

        if (!$model) {
            return false;
        }

        $newModel = clone $model;
        return $newModel;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setType('rule/condition_combine')
            ->setAggregator('all')
            ->setValue(true)
            ->setConditions(array())
            ->setActions(array());


        $this->loadAggregatorOptions();
        if ($options = $this->getAggregatorOptions()) {
            foreach ($options as $aggregator => $dummy) {
                $this->setAggregator($aggregator);
                break;
            }
        }
    }
/* start aggregator methods */
    /**
     * @return $this
     */
    public function loadAggregatorOptions()
    {
        $this->setAggregatorOption(array(
            'all' => Mage::helper('rule')->__('ALL'),
            'any' => Mage::helper('rule')->__('ANY'),
        ));
        return $this;
    }

    /**
     * @return array
     */
    public function getAggregatorSelectOptions()
    {
        $opt = array();
        foreach ($this->getAggregatorOption() as $k => $v) {
            $opt[] = array('value'=>$k, 'label'=>$v);
        }
        return $opt;
    }

    /**
     * @return mixed
     */
    public function getAggregatorName()
    {
        return $this->getAggregatorOption($this->getAggregator());
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getAggregatorElement()
    {
        if (is_null($this->getAggregator())) {
            foreach ($this->getAggregatorOption() as $k => $v) {
                $this->setAggregator($k);
                break;
            }
        }
        return $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__aggregator', 'select', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][aggregator]',
            'values'=>$this->getAggregatorSelectOptions(),
            'value'=>$this->getAggregator(),
            'value_name'=>$this->getAggregatorName(),
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }
/* end aggregator methods */

    /**
     * @return $this|Mage_Rule_Model_Condition_Abstract
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1 => Mage::helper('rule')->__('TRUE'),
            0 => Mage::helper('rule')->__('FALSE'),
        ));
        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function addCondition($condition)
    {
        $condition->setRule($this->getRule());
        $condition->setObject($this->getObject());
        $condition->setPrefix($this->getPrefix());

        $conditions = $this->getConditions();
        $conditions[] = $condition;

        if (!$condition->getId()) {
            $condition->setId($this->getId().'--'.count($conditions));
        }

        $this->setData($this->getPrefix(), $conditions);
        return $this;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Returns array containing conditions in the collection
     *
     * Output example:
     * array(
     *   'type'=>'combine',
     *   'operator'=>'ALL',
     *   'value'=>'TRUE',
     *   'conditions'=>array(
     *     {condition::asArray},
     *     {combine::asArray},
     *     {quote_item_combine::asArray}
     *   )
     * )
     *
     * @param array $arrAttributes
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = parent::asArray();
        $out['aggregator'] = $this->getAggregator();

        foreach ($this->getConditions() as $condition) {
            $out['conditions'][] = $condition->asArray();
        }

        return $out;
    }

    /**
     * @param string $containerKey
     * @param string $itemKey
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = "<aggregator>".$this->getAggregator()."</aggregator>"
            ."<value>".$this->getValue()."</value>"
            ."<$containerKey>";
        foreach ($this->getConditions() as $condition) {
            $xml .= "<$itemKey>".$condition->asXml()."</$itemKey>";
        }
        $xml .= "</$containerKey>";
        return $xml;
    }

    /**
     * @param $arr
     * @param string $key
     * @return $this|Mage_Rule_Model_Condition_Abstract
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAggregator(isset($arr['aggregator']) ? $arr['aggregator']
                : (isset($arr['attribute']) ? $arr['attribute'] : null))
            ->setValue(isset($arr['value']) ? $arr['value']
                : (isset($arr['operator']) ? $arr['operator'] : null));

        if (!empty($arr[$key]) && is_array($arr[$key])) {
            foreach ($arr[$key] as $condArr) {
                try {
                    $cond = $this->_getNewConditionModelInstance($condArr['type']);
                    if ($cond) {
                        $this->addCondition($cond);
                        $cond->loadArray($condArr, $key);
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $this;
    }

    /**
     * @param $xml
     * @return $this|Mage_Rule_Model_Condition_Abstract
     */
    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = parent::loadXml($xml);
        foreach ($xml->conditions->children() as $condition) {
            $arr['conditions'] = parent::loadXml($condition);
        }
        $this->loadArray($arr);
        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
           $html = $this->getTypeElement()->getHtml().
               Mage::helper('rule')->__('If %s of these conditions are %s:', $this->getAggregatorElement()->getHtml(), $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getNewChildElement()
    {
        return $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__new_child', 'select', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][new_child]',
            'values'=>$this->getNewChildSelectOptions(),
            'value_name'=>$this->getNewChildName(),
        ))->setRenderer(Mage::getBlockSingleton('rule/newchild'));
    }

    /**
     * @return string
     */
    public function asHtmlRecursive()
    {
        $html = $this->asHtml().'<ul id="'.$this->getPrefix().'__'.$this->getId().'__children" class="rule-param-children">';
        foreach ($this->getConditions() as $cond) {
            $html .= '<li>'.$cond->asHtmlRecursive().'</li>';
        }
        $html .= '<li>'.$this->getNewChildElement()->getHtml().'</li></ul>';
        return $html;
    }

    /**
     * @param string $format
     * @return string
     */
    public function asString($format = '')
    {
        $str = Mage::helper('rule')->__("If %s of these conditions are %s:", $this->getAggregatorName(), $this->getValueName());
        return $str;
    }

    /**
     * @param int $level
     * @return string
     */
    public function asStringRecursive($level = 0)
    {
        $str = parent::asStringRecursive($level);
        foreach ($this->getConditions() as $cond) {
            $str .= "\n".$cond->asStringRecursive($level+1);
        }
        return $str;
    }

    /**
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all    = $this->getAggregator() === 'all';
        $true   = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validate($object);

            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }

    /**
     * @param $form
     * @return $this
     */
    public function setJsFormObject($form)
    {
        $this->setData('js_form_object', $form);
        foreach ($this->getConditions() as $condition) {
            $condition->setJsFormObject($form);
        }
        return $this;
    }

    /**
     * Get conditions, if current prefix is undefined use 'conditions' key
     *
     * @return array
     */
    public function getConditions()
    {
        $key = $this->getPrefix() ? $this->getPrefix() : 'conditions';
        return $this->getData($key);
    }

    /**
     * Set conditions, if current prefix is undefined use 'conditions' key
     *
     * @param array $conditions
     * @return $this
     */
    public function setConditions($conditions)
    {
        $key = $this->getPrefix() ? $this->getPrefix() : 'conditions';
        return $this->setData($key, $conditions);
    }

    /**
     * Getter for "Conditions Combination" select option for recursive combines
     */
    protected function _getRecursiveChildSelectOption()
    {
        return array('value' => $this->getType(), 'label' => Mage::helper('rule')->__('Conditions Combination'));
    }
}
