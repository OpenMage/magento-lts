<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Failure Response page from iDEAL
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Ideal_Block_Advanced_Failure extends Mage_Core_Block_Template
{
    /**
     *  Returns error from session and clears it
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = Mage::getSingleton('checkout/session')->getIdealErrorMessage();
        Mage::getSingleton('checkout/session')->unsIdealErrorMessage();
        return $error;
    }

    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
