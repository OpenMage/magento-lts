<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart interface
 *
 * @package    Mage_Checkout
 */

interface Mage_Checkout_Model_Cart_Interface
{
    /**
     * Add product to shopping cart (quote)
     *
     * @param   int|Mage_Catalog_Model_Product $productInfo
     * @param   mixed                          $requestInfo
     * @return  Mage_Checkout_Model_Cart_Interface
     */
    public function addProduct($productInfo, $requestInfo = null);

    /**
     * Save cart
     *
     * @abstract
     * @return Mage_Checkout_Model_Cart_Interface
     */
    public function saveQuote();

    /**
     * Associate quote with the cart
     *
     * @abstract
     * @return Mage_Checkout_Model_Cart_Interface
     */
    public function setQuote(Mage_Sales_Model_Quote $quote);

    /**
     * Get quote object associated with cart
     * @abstract
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote();
}
