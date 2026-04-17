<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product attribute api
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Api
{
    /**
     * Create new product attribute
     *
     * @param  array $data input data
     * @return int
     */
    public function create($data)
    {
        $helper = Mage::helper('api');
        $helper->v2AssociativeArrayUnpacker($data);
        Mage::helper('api')->toArray($data);
        return parent::create($data);
    }

    /**
     * Update product attribute
     *
     * @param  int|string $attribute attribute code or ID
     * @param  array      $data
     * @return bool
     */
    public function update($attribute, $data)
    {
        $helper = Mage::helper('api');
        $helper->v2AssociativeArrayUnpacker($data);
        Mage::helper('api')->toArray($data);
        return parent::update($attribute, $data);
    }

    /**
     * Add option to select or multiselect attribute
     *
     * @param  int|string $attribute attribute ID or code
     * @param  array      $data
     * @return bool
     */
    public function addOption($attribute, $data)
    {
        Mage::helper('api')->toArray($data);
        return parent::addOption($attribute, $data);
    }

    /**
     * Get full information about attribute with list of options
     *
     * @param  int|string $attribute attribute ID or code
     * @return array
     */
    public function info($attribute)
    {
        $result = parent::info($attribute);
        if (!empty($result['additional_fields'])) {
            $keys = array_keys($result['additional_fields']);
            foreach ($keys as $key) {
                $result['additional_fields'][] = [
                    'key' => $key,
                    'value' => $result['additional_fields'][$key],
                ];
                unset($result['additional_fields'][$key]);
            }
        }

        return $result;
    }
}
