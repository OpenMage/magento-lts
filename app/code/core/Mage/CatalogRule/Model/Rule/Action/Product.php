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
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_CatalogRule_Model_Rule_Action_Product
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'rule_price'=>Mage::helper('cataloginventory')->__('Rule price'),
        ]);
        return $this;
    }

    /**
     * @return $this|Mage_Rule_Model_Action_Abstract
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption([
            'to_fixed'=>Mage::helper('cataloginventory')->__('To Fixed Value'),
            'to_percent'=>Mage::helper('cataloginventory')->__('To Percentage'),
            'by_fixed'=>Mage::helper('cataloginventory')->__('By Fixed value'),
            'by_percent'=>Mage::helper('cataloginventory')->__('By Percentage'),
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().Mage::helper('catalogrule')->__("Update product's %s %s: %s", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
}
