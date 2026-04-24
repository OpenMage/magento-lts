<?php

declare(strict_types=1);

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
     * @param  array|object $data
     * @return array
     */
    #[Override]
    protected function _prepareProductsData($data)
    {
        if (is_object($data)) {
            $arr = get_object_vars($data);
            foreach ($arr as $key => $value) {
                $assocArr = [];
                if (is_array($value)) {
                    foreach ($value as $item) {
                        if (is_object($item) && count(get_object_vars($item)) == 2
                            && isset($item->key) && isset($item->value)
                        ) {
                            $assocArr[$item->key] = $item->value;
                        }
                    }
                }

                if ($assocArr !== []) {
                    $arr[$key] = $assocArr;
                }
            }

            $arr = $this->_prepareProductsData($arr);
            return parent::_prepareProductsData($arr);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = is_object($value) || is_array($value) ? $this->_prepareProductsData($value) : $value;
            }

            return parent::_prepareProductsData($data);
        }

        return $data;
    }
}
