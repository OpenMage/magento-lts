<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart interface
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Mage_Sales_Model_Quote $quote
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
