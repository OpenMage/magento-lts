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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for available bml banners size
 */
class Mage_Paypal_Model_System_Config_Source_BmlSize
{
    /**
     * Options getter for Home Page and position Header
     *
     * @return array
     */
    public function getBmlSizeHPH()
    {
        return array(
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66')
        );
    }

    /**
     * Options getter for Home Page and position Sidebar (right)
     *
     * @return array
     */
    public function getBmlSizeHPS()
    {
        return array(
            '120x90' => Mage::helper('paypal')->__('120 x 90'),
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '120x240' => Mage::helper('paypal')->__('120 x 240'),
            '120x600' => Mage::helper('paypal')->__('120 x 600'),
            '234x400' => Mage::helper('paypal')->__('234 x 400'),
            '250x250' => Mage::helper('paypal')->__('250 x 250')
        );
    }

    /**
     * Options getter for Catalog Category Page and position Center
     *
     * @return array
     */
    public function getBmlSizeCCPC()
    {
        return array(
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66')
        );
    }

    /**
     * Options getter for Catalog Category Page and position Sidebar (right)
     *
     * @return array
     */
    public function getBmlSizeCCPS()
    {
        return array(
            '120x90' => Mage::helper('paypal')->__('120 x 90'),
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '120x240' => Mage::helper('paypal')->__('120 x 240'),
            '120x600' => Mage::helper('paypal')->__('120 x 600'),
            '234x400' => Mage::helper('paypal')->__('234 x 400'),
            '250x250' => Mage::helper('paypal')->__('250 x 250')
        );
    }

    /**
     * Options getter for Catalog Product Page and position Center
     *
     * @return array
     */
    public function getBmlSizeCPPC()
    {
        return array(
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66')
        );
    }

    /**
     * Options getter for Catalog Product Page and position Near Bill Me Later checkout button
     *
     * @return array
     */
    public function getBmlSizeCPPN()
    {
        return array(
            '120x90' => Mage::helper('paypal')->__('120 x 90'),
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '120x240' => Mage::helper('paypal')->__('120 x 240'),
            '120x600' => Mage::helper('paypal')->__('120 x 600'),
            '234x400' => Mage::helper('paypal')->__('234 x 400'),
            '250x250' => Mage::helper('paypal')->__('250 x 250')
        );
    }

    /**
     * Options getter for Checkout Cart Page and position Center
     *
     * @return array
     */
    public function getBmlSizeCheckoutC()
    {
        return array(
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66')
        );
    }

    /**
     * Options getter for Checkout Cart Page and position Near Bill Me Later checkout button
     *
     * @return array
     */
    public function getBmlSizeCheckoutN()
    {
        return array(
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60')
        );
    }
}
