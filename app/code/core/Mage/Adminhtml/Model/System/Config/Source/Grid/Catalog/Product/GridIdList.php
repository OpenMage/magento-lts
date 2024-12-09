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
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enabled grid column customization
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Grid_Catalog_Product_GridIdList
{
    public function toOptionArray()
    {
        return [
            ['value' => 'productGrid', 'label' => Mage::helper('adminhtml')->__('Catalog Product')],
            ['value' => 'catalog_category_products', 'label' => Mage::helper('adminhtml')->__('Catalog Category Product')],
            ['value' => 'related_product_grid', 'label' => Mage::helper('adminhtml')->__('Catalog Product Related')],
            ['value' => 'up_sell_product_grid', 'label' => Mage::helper('adminhtml')->__('Catalog Product Up-Sells')],
            ['value' => 'cross_sell_product_grid', 'label' => Mage::helper('adminhtml')->__('Catalog Product Cross-Sells')],
        ];
    }
}
