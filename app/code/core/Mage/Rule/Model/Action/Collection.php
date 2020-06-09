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


class Mage_Rule_Model_Action_Collection extends Mage_Rule_Model_Action_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setActions(array());
        $this->setType('rule/action_collection');
    }

    /**
     * Returns array containing actions in the collection
     *
     * Output example:
     * array(
     *   {action::asArray},
     *   {action::asArray}
     * )
     *
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = parent::asArray();

        foreach ($this->getActions() as $item) {
            $out['actions'][] = $item->asArray();
        }
        return $out;
    }

    public function loadArray(array $arr)
    {
        if (!empty($arr['actions']) && is_array($arr['actions'])) {
            foreach ($arr['actions'] as $actArr) {
                if (empty($actArr['type'])) {
                    continue;
                }
                $action = Mage::getModel($actArr['type']);
                $action->loadArray($actArr);
                $this->addAction($action);
            }
        }
        return $this;
    }

    public function addAction(Mage_Rule_Model_Action_Interface $action)
    {
        $actions = $this->getActions();

        $action->setRule($this->getRule());

        $actions[] = $action;
        if (!$action->getId()) {
            $action->setId($this->getId().'.'.count($actions));
        }

        $this->setActions($actions);
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->toHtml().'Perform following actions: ';
        if ($this->getId()!='1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
   public function getNewChildElement()
   {
       return $this->getForm()->addField('action:'.$this->getId().':new_child', 'select', array(
           'name'=>'rule[actions]['.$this->getId().'][new_child]',
           'values'=>$this->getNewChildSelectOptions(),
           'value_name'=>$this->getNewChildName(),
       ))->setRenderer(Mage::getBlockSingleton('rule/newchild'));
    }

    public function asHtmlRecursive()
    {
        $html = $this->asHtml().'<ul id="action:'.$this->getId().':children">';
        foreach ($this->getActions() as $cond) {
            $html .= '<li>'.$cond->asHtmlRecursive().'</li>';
        }
        $html .= '<li>'.$this->getNewChildElement()->getHtml().'</li></ul>';
        return $html;
    }

    public function asString($format='')
    {
        $str = Mage::helper('rule')->__("Perform following actions");
        return $str;
    }

    public function asStringRecursive($level=0)
    {
        $str = $this->asString();
        foreach ($this->getActions() as $action) {
            $str .= "\n".$action->asStringRecursive($level+1);
        }
        return $str;
    }

    public function process()
    {
        foreach ($this->getActions() as $action) {
            $action->process();
        }
        return $this;
    }
}
