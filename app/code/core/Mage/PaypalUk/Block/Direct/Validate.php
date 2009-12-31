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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*
 * Display PayPalUk Direct payment form
 */
class Mage_PaypalUk_Block_Direct_Validate extends Mage_Payment_Block_Form
{
    /*
     * Setup customized value for PayPalUk Direct Form
     * @return Mage_PaypalUk_Block_Direct_Validate
     */
    protected function _construct()
    {
        parent::_construct();
        return $this;
    }

    /**
     * Return 3d secure validation object
     *
     * @return Mage_PaypalUk_Model_Direct_Validate
     */
    public function getValidation()
    {
        return Mage::getSingleton('paypaluk/direct_validate');
    }

    /**
     * Return acsUrl, url to cardholder bank account validation
     *
     * @return string
     */
    public function getACSUrl()
    {
        return $this->getValidation()->getACSUrl();
    }

    /**
     * Return exncripted code, result 3d secure lookup api call
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->getValidation()->getPayload();
    }

    /**
     * Return url, customer will redirect to this url after success verification
     *
     * @return string
     */
    public function getTermUrl()
    {
        return $this->getValidation()->getTermUrl();
    }

    /**
     * return transaction id. result of 3d secure lookup api call
     *
     * @return string
     */
    public function getTocken()
    {
        return $this->getValidation()->getTocken();
    }
}
