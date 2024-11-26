<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Renderer for Qty field in sales create new order search grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    /**
     * Returns whether this qty field must be inactive
     *
     * @param   Varien_Object $row
     * @return  bool
     */
    protected function _isInactive($row)
    {
        return $row->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE;
    }

    /**
     * Render product qty field
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        // Prepare values
        $isInactive = $this->_isInactive($row);

        if ($isInactive) {
            $qty = '';
        } else {
            $qty = $row->getData($this->getColumn()->getIndex());
            $qty *= 1;
            if (!$qty) {
                $qty = '';
            }
        }

        // Compose html
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $qty . '" ';
        if ($isInactive) {
            $html .= 'disabled="disabled" ';
        }
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . ($isInactive ? ' input-inactive' : '') . '" />';
        return $html;
    }
}
