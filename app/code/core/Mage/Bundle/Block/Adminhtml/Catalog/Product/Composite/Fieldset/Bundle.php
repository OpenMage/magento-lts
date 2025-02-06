<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Adminhtml block for fieldset of bundle product
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Bundle extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    /**
     * Returns string with json config for bundle product
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $options = [];
        $optionsArray = $this->getOptions();
        foreach ($optionsArray as $option) {
            $optionId = $option->getId();
            $options[$optionId] = ['id' => $optionId, 'selections' => []];
            foreach ($option->getSelections() as $selection) {
                $options[$optionId]['selections'][$selection->getSelectionId()] = [
                    'can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'default_qty'    => $selection->getSelectionQty(),
                ];
            }
        }
        $config = ['options' => $options];
        return Mage::helper('core')->jsonEncode($config);
    }
}
