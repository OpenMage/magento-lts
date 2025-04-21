<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Configurable product assocciated products grid in stock renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column value
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $inStock = $this->_getValue($row);
        return $inStock ?
               Mage::helper('catalog')->__('In Stock')
               : Mage::helper('catalog')->__('Out of Stock');
    }
}
