<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product tier price api V2
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Tierprice_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Tierprice_Api
{
    /**
     *  Prepare tier prices for save
     *
     *  @param      Mage_Catalog_Model_Product $product
     *  @param      array $tierPrices
     *  @return     null|array
     */
    public function prepareTierPrices($product, $tierPrices = null)
    {
        if (!is_array($tierPrices)) {
            return null;
        }

        $updateValue = [];

        foreach ($tierPrices as $tierPrice) {
            if (!is_object($tierPrice)
                || !isset($tierPrice->qty)
                || !isset($tierPrice->price)
            ) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
            }

            if (!isset($tierPrice->website) || $tierPrice->website == 'all') {
                $tierPrice->website = 0;
            } else {
                try {
                    $tierPrice->website = Mage::app()->getWebsite($tierPrice->website)->getId();
                } catch (Mage_Core_Exception) {
                    $tierPrice->website = 0;
                }
            }

            if ((int) $tierPrice->website > 0 && !in_array($tierPrice->website, $product->getWebsiteIds())) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid tier prices. The product is not associated to the requested website.'));
            }

            if (!isset($tierPrice->customer_group_id)) {
                $tierPrice->customer_group_id = 'all';
            }

            if ($tierPrice->customer_group_id == 'all') {
                $tierPrice->customer_group_id = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }

            $updateValue[] = [
                'website_id' => $tierPrice->website,
                'cust_group' => $tierPrice->customer_group_id,
                'price_qty'  => $tierPrice->qty,
                'price'      => $tierPrice->price,
            ];
        }

        return $updateValue;
    }
}
