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
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable checkout success page
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{
    /**
     * Return true if order(s) has one or more downloadable products
     *
     * @return bool
     */
    public function getOrderHasDownloadable()
    {
        $hasDownloadableFlag = Mage::getSingleton('checkout/session')
            ->getHasDownloadableProducts(true);
        if (!$this->isOrderVisible()) {
            return false;
        }
        /**
         * if use guest checkout
         */
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            return false;
        }
        return $hasDownloadableFlag;
    }

    /**
     * Return url to list of ordered downloadable products of customer
     *
     * @return string
     */
    public function getDownloadableProductsUrl()
    {
        return $this->getUrl('downloadable/customer/products', ['_secure' => true]);
    }
}
