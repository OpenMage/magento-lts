<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart api for product
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Product_Api_V2 extends Mage_Checkout_Model_Cart_Product_Api
{
    /**
     * Return an Array of Object attributes.
     *
     * @param object|array $data
     * @return array
     */
    protected function _prepareProductsData($data)
    {
        if (is_object($data)) {
            $arr = get_object_vars($data);
            foreach ($arr as $key => $value) {
                $assocArr = [];
                if (is_array($value)) {
                    foreach ($value as $v) {
                        if (is_object($v) && count(get_object_vars($v)) == 2
                            && isset($v->key) && isset($v->value)
                        ) {
                            $assocArr[$v->key] = $v->value;
                        }
                    }
                }
                if (!empty($assocArr)) {
                    $arr[$key] = $assocArr;
                }
            }
            $arr = $this->_prepareProductsData($arr);
            return parent::_prepareProductsData($arr);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $data[$key] = $this->_prepareProductsData($value);
                } else {
                    $data[$key] = $value;
                }
            }
            return parent::_prepareProductsData($data);
        }
        return $data;
    }
}
