<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product tier price api
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Tierprice_Api extends Mage_Catalog_Model_Api_Resource
{
    public const ATTRIBUTE_CODE = 'tier_price';

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * @param int $productId
     * @param null|string $identifierType
     * @return array
     * @throws Mage_Core_Exception
     */
    public function info($productId, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $identifierType);
        $tierPrices = $product->getData(self::ATTRIBUTE_CODE);

        if (!is_array($tierPrices)) {
            return [];
        }

        $result = [];

        foreach ($tierPrices as $tierPrice) {
            $row = [];
            $row['customer_group_id'] = (empty($tierPrice['all_groups']) ? $tierPrice['cust_group'] : 'all');
            $row['website']           = (
                $tierPrice['website_id']
                    ? Mage::app()->getWebsite($tierPrice['website_id'])->getCode()
                    : 'all'
            );
            $row['qty']               = $tierPrice['price_qty'];
            $row['price']             = $tierPrice['price'];

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Update tier prices of product
     *
     * @param int|string $productId
     * @param array $tierPrices
     * @param null|string $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function update($productId, $tierPrices, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $identifierType);

        $updatedTierPrices = $this->prepareTierPrices($product, $tierPrices);
        if (is_null($updatedTierPrices)) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
        }

        $product->setData(self::ATTRIBUTE_CODE, $updatedTierPrices);
        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = [];
                foreach ($errors as $code => $error) {
                    $strErrors[] = ($error === true) ? Mage::helper('catalog')->__('Value for "%s" is invalid.', $code) : Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
                }

                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('not_updated', $mageCoreException->getMessage());
        }

        return true;
    }

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
            if (!is_array($tierPrice)
                || !isset($tierPrice['qty'])
                || !isset($tierPrice['price'])
            ) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
            }

            if (!isset($tierPrice['website']) || $tierPrice['website'] == 'all') {
                $tierPrice['website'] = 0;
            } else {
                try {
                    $tierPrice['website'] = Mage::app()->getWebsite($tierPrice['website'])->getId();
                } catch (Mage_Core_Exception) {
                    $tierPrice['website'] = 0;
                }
            }

            if ((int) $tierPrice['website'] > 0 && !in_array($tierPrice['website'], $product->getWebsiteIds())) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid tier prices. The product is not associated to the requested website.'));
            }

            if (!isset($tierPrice['customer_group_id'])) {
                $tierPrice['customer_group_id'] = 'all';
            }

            if ($tierPrice['customer_group_id'] == 'all') {
                $tierPrice['customer_group_id'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }

            $updateValue[] = [
                'website_id' => $tierPrice['website'],
                'cust_group' => $tierPrice['customer_group_id'],
                'price_qty'  => $tierPrice['qty'],
                'price'      => $tierPrice['price'],
            ];
        }

        return $updateValue;
    }

    /**
     * Retrieve product
     *
     * @param int $productId
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct($productId, $identifierType = null)
    {
        $product = Mage::helper('catalog/product')->getProduct($productId, $this->_getStoreId(), $identifierType);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }
}
