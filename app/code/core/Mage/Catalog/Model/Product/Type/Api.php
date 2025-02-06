<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog product type api
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Type_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve product type list
     *
     * @return array
     */
    public function items()
    {
        $result = [];

        foreach (Mage_Catalog_Model_Product_Type::getOptionArray() as $type => $label) {
            $result[] = [
                'type'  => $type,
                'label' => $label,
            ];
        }

        return $result;
    }
}
