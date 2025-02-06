<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Report Customers Reviews renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $id   = $row->getCustomerId();

        if (!$id) {
            return Mage::helper('adminhtml')->__('Show Reviews');
        }

        return sprintf(
            '<a href="%s">%s</a>',
            $this->getUrl('*/catalog_product_review', ['customerId' => $id]),
            Mage::helper('adminhtml')->__('Show Reviews'),
        );
    }
}
