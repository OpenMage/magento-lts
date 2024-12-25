<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote rule action abstract
 *
 * @category   Mage
 * @package    Mage_Rule
 *
 * @method array getAttributeOption()
 * @method $this setAttributeOption(array $value)
 * @method array getOperatorOption()
 * @method $this setOperatorOption(array $value)
 * @method array getValueOption()
 * @method $this setValueOption(array $value)
 * @method string getAttribute()
 * @method $this setAttribute(string $value)
 * @method string getOperator()
 * @method $this setOperator(string $value)
 * @method string getType()
 * @method string getValue()
 * @method Mage_Rule_Model_Abstract getRule()
 */
abstract class Mage_Rule_Model_Action_Abstract extends Varien_Object implements Mage_Rule_Model_Action_Interface
{
    /**
     * Flag to enable translation for loadOperatorOptions/loadValueOptions/loadAggregatorOptions/getDefaultOperatorOptions
     * It's useless to translate these data on frontend
     *
     * @var bool
     */
    protected static $translate;

    public function __construct()
    {
        if (!is_bool(static::$translate)) {
            static::$translate = Mage::app()->getStore()->isAdmin();
        }

        parent::__construct();
        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();

        foreach (array_keys($this->getAttributeOption()) as $attr) {
            $this->setAttribute($attr);
            break;
        }
        foreach (array_keys($this->getOperatorOption()) as $operator) {
            $this->setOperator($operator);
            break;
        }
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
            'type' => $this->getType(),
            'attribute' => $this->getAttribute(),
            'operator' => $this->getOperator(),
            'value' => $this->getValue(),
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
     * @return $this
     */
    public function loadArray(array $arr)
    {
        $this->addData([
            'type' => $arr['type'],
            'attribute' => $arr['attribute'],
            'operator' => $arr['operator'],
            'value' => $arr['value'],
        ]);
        $this->loadAttributeOptions();
        $this->loadOperatorOptions();
        $this->loadValueOptions();
        return $this;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([]);
        return $this;
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
     * @return mixed
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
        $this->setOperatorOption([
            '='  => static::$translate ? Mage::helper('rule')->__('to') : 'to',
            '+=' => static::$translate ? Mage::helper('rule')->__('by') : 'by',
        ]);
        return $this;
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        $opt = [];
        foreach ($this->getOperatorOption() as $k => $v) {
            $opt[] = ['value' => $k, 'label' => $v];
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
        $opt = [];
        foreach ($this->getValueOption() as $k => $v) {
            $opt[] = ['value' => $k, 'label' => $v];
        }
        return $opt;
    }

    /**
     * @return string
     */
    public function getValueName()
    {
        $value = $this->getValue();
        return !empty($value) || $value === 0 ? $value : '...';
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [
            ['value' => '', 'label' => Mage::helper('rule')->__('Please choose an action to add...')],
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
        return '';
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
        return $this->getForm()->addField('action:' . $this->getId() . ':type', 'hidden', [
            'name' => 'rule[actions][' . $this->getId() . '][type]',
            'value' => $this->getType(),
            'no_span' => true,
        ]);
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':attribute', 'select', [
            'name' => 'rule[actions][' . $this->getId() . '][attribute]',
            'values' => $this->getAttributeSelectOptions(),
            'value' => $this->getAttribute(),
            'value_name' => $this->getAttributeName(),
        ])->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getOperatorElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':operator', 'select', [
            'name' => 'rule[actions][' . $this->getId() . '][operator]',
            'values' => $this->getOperatorSelectOptions(),
            'value' => $this->getOperator(),
            'value_name' => $this->getOperatorName(),
        ])->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':value', 'text', [
            'name' => 'rule[actions][' . $this->getId() . '][value]',
            'value' => $this->getValue(),
            'value_name' => $this->getValueName(),
        ])->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    /**
     * @return string
     */
    public function getAddLinkHtml()
    {
        $src = Mage::getDesign()->getSkinUrl('images/rule_component_add.gif');
        return '<img src="' . $src . '" alt="" class="rule-param-add v-middle" />';
    }

    /**
     * @return string
     */
    public function getRemoveLinkHtml()
    {
        $src = Mage::getDesign()->getSkinUrl('images/rule_component_remove.gif');
        return '<span class="rule-param"><a href="javascript:void(0)" class="rule-param-remove"><img src="'
            . $src . '" alt="" class="v-middle" /></a></span>';
    }

    /**
     * @param string $format
     * @return string
     */
    public function asString($format = '')
    {
        return '';
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
     * @return $this
     */
    public function process()
    {
        return $this;
    }
}
