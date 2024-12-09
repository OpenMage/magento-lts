<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for available bml banners size
 *
 * @category   Mage
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
            '800x66' => Mage::helper('paypal')->__('800 x 66')
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
            '250x250' => Mage::helper('paypal')->__('250 x 250')
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
            '800x66' => Mage::helper('paypal')->__('800 x 66')
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
            '250x250' => Mage::helper('paypal')->__('250 x 250')
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
            '800x66' => Mage::helper('paypal')->__('800 x 66')
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
            '250x250' => Mage::helper('paypal')->__('250 x 250')
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
            '800x66' => Mage::helper('paypal')->__('800 x 66')
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
            '468x60' => Mage::helper('paypal')->__('468 x 60')
        ];
    }
}
