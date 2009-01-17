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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract class for quote rule condition
 *
 */
abstract class Mage_Rule_Model_Condition_Abstract
	extends Varien_Object
	implements Mage_Rule_Model_Condition_Interface
{
    public function __construct()
    {
        parent::__construct();

        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();

        if ($options = $this->getAttributeOptions()) {
            foreach ($options as $attr=>$dummy) { $this->setAttribute($attr); break; }
        }
        if ($options = $this->getOperatorOptions()) {
            foreach ($options as $operator=>$dummy) { $this->setOperator($operator); break; }
        }
    }

    public function getForm()
    {
        return $this->getRule()->getForm();
    }

    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'type'=>$this->getType(),
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
            'value'=>$this->getValue(),
            'is_value_processed'=>$this->getIsValueParsed(),
        );
        return $out;
    }

    public function asXml()
    {
        $xml = "<type>".$this->getType()."</type>"
            ."<attribute>".$this->getAttribute()."</attribute>"
            ."<operator>".$this->getOperator()."</operator>"
            ."<value>".$this->getValue()."</value>";
        return $xml;
    }

    public function loadArray($arr)
    {
        $this->setType($arr['type']);
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $this->setOperator(isset($arr['operator']) ? $arr['operator'] : false);
        $this->setValue(isset($arr['value']) ? $arr['value'] : false);
        $this->setIsValueParsed(isset($arr['is_value_parsed']) ? $arr['is_value_parsed'] : false);

//        $this->loadAttributeOptions();
//        $this->loadOperatorOptions();
//        $this->loadValueOptions();
        return $this;
    }

    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = (array)$xml;
        $this->loadArray($arr);
        return $this;
    }

    public function loadAttributeOptions()
    {
        return $this;
    }

    public function getAttributeOptions()
    {
        return array();
    }

    public function getAttributeSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getAttributeOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
    }

    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }

    public function loadOperatorOptions()
    {
        $hlp = Mage::helper('rule');
        $this->setOperatorOption(array(
            '=='  => $hlp->__('is'),
            '!='  => $hlp->__('is not'),
            '>='  => $hlp->__('equals or greater than'),
            '<='  => $hlp->__('equals or less than'),
            '>'   => $hlp->__('greater than'),
            '<'   => $hlp->__('less than'),
            '{}'  => $hlp->__('contains'),
            '!{}' => $hlp->__('does not contain'),
            '()'  => $hlp->__('is one of'),
            '!()' => $hlp->__('is not one of'),
        ));
        $this->setOperatorByInputType(array(
            'string' => array('==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'),
            'numeric' => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),
            'date' => array('==', '>=', '<='),
            'select' => array('==', '!='),
            'grid' => array('()', '!()'),
        ));
        return $this;
    }

    /**
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid
     *
     * @return string
     */
    public function getInputType()
    {
        return 'string';
    }

    public function getOperatorSelectOptions()
    {
        $type = $this->getInputType();
    	$opt = array();
    	$operatorByType = $this->getOperatorByInputType();
    	foreach ($this->getOperatorOption() as $k=>$v) {
    	    if (!$operatorByType || in_array($k, $operatorByType[$type])) {
    		    $opt[] = array('value'=>$k, 'label'=>$v);
    	    }
    	}
    	return $opt;
    }

    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }

    public function loadValueOptions()
    {
//        $this->setValueOption(array(
//            true  => Mage::helper('rule')->__('TRUE'),
//            false => Mage::helper('rule')->__('FALSE'),
//        ));
        $this->setValueOption(array());
        return $this;
    }

    public function getValueSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getValueOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
    }

    public function getValueParsed()
    {
        $value = $this->getData('value');

        $op = $this->getOperator();
        if (($op==='()' || $op==='!()') && is_string($value)) {
            $value = preg_split('#\s*[,;]\s*#', $value, null, PREG_SPLIT_NO_EMPTY);
            $this->setValue($value);
        }

        return $value;
    }

    public function getValue()
    {
        if ($this->getInputType()=='date' && !$this->getIsValueParsed()) {
            $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d', $this->getData('value'));
            $this->setValue($date);
            $this->setIsValueParsed(true);
        }
        return $this->getData('value');
    }

    public function getValueName()
    {
        $value = $this->getValue();
        if (is_null($value) || ''===$value) {
            return '...';
        }

        if ($this->getInputType()=='date') {
            return Mage::helper('core')->formatDate($value);
        }

        $options = $this->getValueSelectOptions();
        if (!empty($options)) {
            foreach ($options as $o) {
                if (is_array($o['value'])) {
                    foreach ($o['value'] as $v) {
                        if ($v['value']==$value) {
                            return $v['label'];
                        }
                    }
                }
                if ($o['value']==$value) {
                    return $o['label'];
                }
            }
        }

        return $value;
    }

    public function getNewChildSelectOptions()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('rule')->__('Please choose a condition to add...')),
        );
    }

    public function getNewChildName()
    {
        return $this->getAddLinkHtml();
    }

    public function asHtml()
    {
    	$html = $this->getTypeElementHtml()
    	   .$this->getAttributeElementHtml()
           .$this->getOperatorElementHtml()
    	   .$this->getValueElementHtml()
    	   .$this->getRemoveLinkHtml()
    	   .$this->getChooserContainerHtml();
    	return $html;
    }

    public function asHtmlRecursive()
    {
        $html = $this->asHtml();
        return $html;
    }

    public function getTypeElement()
    {
    	return $this->getForm()->addField($this->getPrefix().':'.$this->getId().':type', 'hidden', array(
    		'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][type]',
    		'value'=>$this->getType(),
    		'no_span'=>true,
    	));
    }

    public function getTypeElementHtml()
    {
        return $this->getTypeElement()->getHtml();
    }

    public function getAttributeElement()
    {
        if (is_null($this->getAttribute())) {
            foreach ($this->getAttributeOption() as $k=>$v) {
                $this->setAttribute($k);
                break;
            }
        }
    	return $this->getForm()->addField($this->getPrefix().':'.$this->getId().':attribute', 'select', array(
    		'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][attribute]',
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    public function getAttributeElementHtml()
    {
        return $this->getAttributeElement()->getHtml();
    }

    public function getOperatorElement()
    {
        if (is_null($this->getOperator())) {
            foreach ($this->getOperatorOption() as $k=>$v) {
                $this->setOperator($k);
                break;
            }
        }
        return $this->getForm()->addField($this->getPrefix().':'.$this->getId().':operator', 'select', array(
    		'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][operator]',
    		'values'=>$this->getOperatorSelectOptions(),
    		'value'=>$this->getOperator(),
    		'value_name'=>$this->getOperatorName(),
    	))->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

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

    public function getValueElementRenderer()
    {
        if (strpos($this->getValueElementType(), '/')!==false) {
            return Mage::getBlockSingleton($this->getValueElementType());
        }
        return Mage::getBlockSingleton('rule/editable');
    }

    public function getValueElement()
    {
        $value = $this->getData('value');
        if ($this->getInputType()=='date') {
            $value = $this->getValueName();
        }

        return $this->getForm()->addField($this->getPrefix().':'.$this->getId().':value',
            $this->getValueElementType(),
            array(
        		'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][value]',
        		'value'=>$value,
        		'values'=>$this->getValueSelectOptions(),
        		'value_name'=>$this->getValueName(),
        		'after_element_html'=>$this->getValueAfterElementHtml(),
        		'explicit_apply'=>$this->getExplicitApply(),
    	   )
    	)->setRenderer($this->getValueElementRenderer());
    }

    public function getValueElementHtml()
    {
        return $this->getValueElement()->getHtml();
    }

    public function getAddLinkHtml()
    {
    	$src = Mage::getDesign()->getSkinUrl('images/rule_component_add.gif');
    	$html = '<img src="'.$src.'" class="rule-param-add v-middle" title="'.Mage::helper('rule')->__('Add').'"/>';
        return $html;
    }

    public function getRemoveLinkHtml()
    {
    	$src = Mage::getDesign()->getSkinUrl('images/rule_component_remove.gif');
        $html = ' <span class="rule-param"><a href="javascript:void(0)" class="rule-param-remove" title="'.Mage::helper('rule')->__('Remove').'"><img src="'.$src.'" class="v-middle"/></a></span>';
        return $html;
    }

    public function getChooserContainerHtml()
    {
        $url = $this->getValueElementChooserUrl();
        $html = '';
        if ($url) {
            $html = '<div class="rule-chooser" url="'.$url.'"></div>';
        }
        return $html;
    }

    public function asString($format='')
    {
        $str = $this->getAttributeName().' '.$this->getOperatorName().' '.$this->getValueName();
        return $str;
    }

    public function asStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->asString();
        return $str;
    }

    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        $value = $this->getValueParsed();
        $op = $this->getOperator();

        // if operator requires array and it is not, or on opposite, return false
        if ((($op=='()' || $op=='!()') && !is_array($value))
            || (!($op=='()' || $op=='!()') && is_array($value))) {
            return false;
        }

        $result = false;

        switch ($op) {
            case '==': case '!=':
                if (is_array($validatedValue)) {
                    $result = in_array($value, $validatedValue);
                } else {
                    $result = $validatedValue==$value;
                }
                break;

            case '<=': case '>':
                if (is_array($validatedValue)) {
                    $result = false;
                } else {
                    $result = $validatedValue<=$value;
                }
                break;

            case '>=': case '<':
                if (is_array($validatedValue)) {
                    $result = false;
                } else {
                    $result = $validatedValue>=$value;
                }
                break;

            case '{}': case '!{}':
                if (is_array($validatedValue)) {
                    $result = false;
                } else {
                    $result = stripos((string)$validatedValue, (string)$value)!==false;
                }
                break;

            case '()': case '!()':
                if (is_array($validatedValue)) {
                    $result = count(array_intersect($validatedValue, (array)$value))>0;
                } else {
                    $result = in_array($validatedValue, (array)$value);
                }
                break;
        }

        if ('!='==$op || '>'==$op || '<'==$op || '!{}'==$op || '!()'==$op) {
            $result = !$result;
        }

        return $result;
    }

    public function validate(Varien_Object $object)
    {
        return $this->validateAttribute($object->getData($this->getAttribute()));
    }
}