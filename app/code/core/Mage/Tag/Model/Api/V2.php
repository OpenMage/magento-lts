<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Product Tag API
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Api_V2 extends Mage_Tag_Model_Api
{
    /**
     * Retrieve list of tags for specified product as array of objects
     *
     * @param  int        $productId
     * @param  int|string $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = parent::items($productId, $store);
        foreach ($result as $key => $tag) {
            $result[$key] = Mage::helper('api')->wsiArrayPacker($tag);
        }

        return array_values($result);
    }

    /**
     * Add tag(s) to product.
     * Return array of objects
     *
     * @param  array $data
     * @return array
     */
    public function add($data)
    {
        $result = [];
        foreach (parent::add($data) as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
        }

        return $result;
    }

    /**
     * Retrieve tag info as object
     *
     * @param  int          $tagId
     * @param  int|string   $store
     * @return array|object
     */
    public function info($tagId, $store)
    {
        $result = parent::info($tagId, $store);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->products as $key => $value) {
            $result->products[$key] = ['key' => $key, 'value' => $value];
        }

        return $result;
    }

    /**
     * Convert data from object to array before add
     *
     * @param  array|object $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        Mage::helper('api')->toArray($data);
        return parent::_prepareDataForAdd($data);
    }

    /**
     * Convert data from object to array before update
     *
     * @param  array|object $data
     * @return array
     */
    protected function _prepareDataForUpdate($data)
    {
        Mage::helper('api')->toArray($data);
        return parent::_prepareDataForUpdate($data);
    }
}
