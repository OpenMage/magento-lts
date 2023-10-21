<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_CatalogRule_Model_Rule_Action_Product
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 *
 * @method $this setAttributeOption(array $value)
 * @method $this setOperatorOption(array $value)
 */
class Mage_CatalogRule_Model_Rule_Action_Product extends Mage_Rule_Model_Action_Abstract
{
    /**
     * @return $this|Mage_Rule_Model_Action_Abstract
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'rule_price' => Mage::helper('cataloginventory')->__('Rule price'),
        ]);
        return $this;
    }

    /**
     * @return $this|Mage_Rule_Model_Action_Abstract
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption([
            'to_fixed'   => static::$translate ? Mage::helper('cataloginventory')->__('To Fixed Value') : 'To Fixed Value',
            'to_percent' => static::$translate ? Mage::helper('cataloginventory')->__('To Percentage') : 'To Percentage',
            'by_fixed'   => static::$translate ? Mage::helper('cataloginventory')->__('By Fixed value') : 'By Fixed value',
            'by_percent' => static::$translate ? Mage::helper('cataloginventory')->__('By Percentage') : 'By Percentage',
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('catalogrule')->__("Update product's %s %s: %s", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        $html .= $this->getRemoveLinkHtml();
        return $html;
    }
}
