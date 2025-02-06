<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Product form weight field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight extends Varien_Data_Form_Element_Text
{
    /**
     * Validation classes for weight field which corresponds to DECIMAL(12,4) SQL type
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->addClass('validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999');
    }
}
