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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Renderer for Qty field in sales create new order search grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param   Varien_Object $row
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
