<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist block customer item cart column
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Returns qty to show visually to user
     *
     * @return float
     */
    public function getAddToCartQty(Mage_Wishlist_Model_Item $item)
    {
        $qty = $item->getQty();
        return $qty ? $qty : 1;
    }

    /**
     * Retrieve column related javascript code
     *
     * @return string
     */
    public function getJs()
    {
        $js = "
            function addWItemToCart(itemId) {
                addWItemToCartCustom(itemId, true)
            }

            function addWItemToCartCustom(itemId, sendGet) {
                var url = '';
                if (sendGet) {
                    url = '" . $this->getItemAddToCartUrl('%item%') . "';
                } else {
                    url = '" . $this->getItemAddToCartUrlCustom('%item%', false) . "';
                }
                url = url.replace('%item%', itemId);

                var form = document.getElementById('wishlist-view-form');
                if (form) {
                    var input = form['qty[' + itemId + ']'];
                    if (input) {
                        var separator = (url.indexOf('?') >= 0) ? '&' : '?';
                        url += separator + input.name + '=' + encodeURIComponent(input.value);
                    }
                }
                if (sendGet) {
                    window.location.href = encodeURI(url);
                } else {
                    customFormSubmit(url, '" . json_encode(['form_key' => $this->getFormKey()]) . "', 'post');
                }
            }
        ";
        return $js . parent::getJs();
    }
}
