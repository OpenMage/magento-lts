<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Source model for available bml banners size
 *
 * @package    Mage_Paypal
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
        return [
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66'),
        ];
    }

    /**
     * Options getter for Home Page and position Sidebar (right)
     *
     * @return array
     */
    public function getBmlSizeHPS()
    {
        return [
            '120x90' => Mage::helper('paypal')->__('120 x 90'),
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '120x240' => Mage::helper('paypal')->__('120 x 240'),
            '120x600' => Mage::helper('paypal')->__('120 x 600'),
            '234x400' => Mage::helper('paypal')->__('234 x 400'),
            '250x250' => Mage::helper('paypal')->__('250 x 250'),
        ];
    }

    /**
     * Options getter for Catalog Category Page and position Center
     *
     * @return array
     */
    public function getBmlSizeCCPC()
    {
        return [
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66'),
        ];
    }

    /**
     * Options getter for Catalog Category Page and position Sidebar (right)
     *
     * @return array
     */
    public function getBmlSizeCCPS()
    {
        return [
            '120x90' => Mage::helper('paypal')->__('120 x 90'),
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '120x240' => Mage::helper('paypal')->__('120 x 240'),
            '120x600' => Mage::helper('paypal')->__('120 x 600'),
            '234x400' => Mage::helper('paypal')->__('234 x 400'),
            '250x250' => Mage::helper('paypal')->__('250 x 250'),
        ];
    }

    /**
     * Options getter for Catalog Product Page and position Center
     *
     * @return array
     */
    public function getBmlSizeCPPC()
    {
        return [
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66'),
        ];
    }

    /**
     * Options getter for Catalog Product Page and position Near Bill Me Later checkout button
     *
     * @return array
     */
    public function getBmlSizeCPPN()
    {
        return [
            '120x90' => Mage::helper('paypal')->__('120 x 90'),
            '190x100' => Mage::helper('paypal')->__('190 x 100'),
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '120x240' => Mage::helper('paypal')->__('120 x 240'),
            '120x600' => Mage::helper('paypal')->__('120 x 600'),
            '234x400' => Mage::helper('paypal')->__('234 x 400'),
            '250x250' => Mage::helper('paypal')->__('250 x 250'),
        ];
    }

    /**
     * Options getter for Checkout Cart Page and position Center
     *
     * @return array
     */
    public function getBmlSizeCheckoutC()
    {
        return [
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
            '728x90' => Mage::helper('paypal')->__('728 x 90'),
            '800x66' => Mage::helper('paypal')->__('800 x 66'),
        ];
    }

    /**
     * Options getter for Checkout Cart Page and position Near Bill Me Later checkout button
     *
     * @return array
     */
    public function getBmlSizeCheckoutN()
    {
        return [
            '234x60' => Mage::helper('paypal')->__('234 x 60'),
            '300x50' => Mage::helper('paypal')->__('300 x 50'),
            '468x60' => Mage::helper('paypal')->__('468 x 60'),
        ];
    }
}
