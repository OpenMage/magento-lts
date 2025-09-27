<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product type api
 *
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
