<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tax rates grid item renderer country
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rate_Grid_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Country
{
    /**
     * Render column for export
     *
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
