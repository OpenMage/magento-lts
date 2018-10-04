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
 * @package     Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogRule_Model_Rule_Action_Product extends Mage_Rule_Model_Action_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'rule_price'=>Mage::helper('cataloginventory')->__('Rule price'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            'to_fixed'=>Mage::helper('cataloginventory')->__('To Fixed Value'),
            'to_percent'=>Mage::helper('cataloginventory')->__('To Percentage'),
            'by_fixed'=>Mage::helper('cataloginventory')->__('By Fixed value'),
            'by_percent'=>Mage::helper('cataloginventory')->__('By Percentage'),
        ));
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().Mage::helper('catalogrule')->__("Update product's %s %s: %s", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
}
