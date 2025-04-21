<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Rule_Action_Product extends Mage_Rule_Model_Action_Abstract
{
    /**
     * @return $this|Mage_Rule_Model_Action_Abstract
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'rule_price' => Mage::helper('salesrule')->__('Special Price'),
        ]);
        return $this;
    }

    /**
     * @return $this|Mage_Rule_Model_Action_Abstract
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption([
            'to_fixed'   => static::$translate ? Mage::helper('salesrule')->__('To Fixed Value') : 'To Fixed Value',
            'to_percent' => static::$translate ? Mage::helper('salesrule')->__('To Percentage') : 'To Percentage',
            'by_fixed'   => static::$translate ? Mage::helper('salesrule')->__('By Fixed value') : 'By Fixed value',
            'by_percent' => static::$translate ? Mage::helper('salesrule')->__('By Percentage') : 'By Percentage',
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('salesrule')->__("Update product's %s %s: %s", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        return $html . $this->getRemoveLinkHtml();
    }
}
