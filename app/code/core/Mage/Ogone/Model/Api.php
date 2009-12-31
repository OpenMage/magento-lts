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
 * @package     Mage_Ogone
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ogone payment method model
 */
class Mage_Ogone_Model_Api extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ogone';
    protected $_formBlockType = 'ogone/form';
    protected $_infoBlockType = 'ogone/info';
    protected $_config = null;

     /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    /* Ogone template modes */
    const TEMPLATE_OGONE            = 'ogone';
    const TEMPLATE_MAGENTO          = 'magento';

    /* Ogone payment process statuses */
    const PENDING_OGONE_STATUS      = 'pending_ogone';
    const CANCEL_OGONE_STATUS       = 'cancel_ogone';
    const DECLINE_OGONE_STATUS      = 'decline_ogone';
    const PROCESSING_OGONE_STATUS   = 'processing_ogone';
    const WAITING_AUTHORIZATION     = 'waiting_authorozation';
    const PROCESSED_OGONE_STATUS    = 'processed_ogone';

    /* Ogone responce statuses */
    const OGONE_PAYMENT_REQUESTED_STATUS    = 9;
    const OGONE_PAYMENT_PROCESSING_STATUS   = 91;
    const OGONE_AUTH_UKNKOWN_STATUS         = 52;
    const OGONE_PAYMENT_UNCERTAIN_STATUS    = 92;
    const OGONE_PAYMENT_INCOMPLETE          = 1;
    const OGONE_AUTH_REFUZED                = 2;
    const OGONE_AUTH_PROCESSING             = 51;
    const OGONE_TECH_PROBLEM                = 93;
    const OGONE_AUTHORIZED                  = 5;

    /* Layout of the payment method */
    const PMLIST_HORISONTAL_LEFT            = 0;
    const PMLIST_HORISONTAL                 = 1;
    const PMLIST_VERTICAL                   = 2;

    /* ogone paymen action constant*/
    const OGONE_AUTHORIZE_ACTION = 'RES';
    const OGONE_AUTHORIZE_CAPTURE_ACTION = 'SAL';

    /**
     * Init Ogone Api instance, detup default values
     *
     * @return Mage_Ogone_Model_Api
     */
    public function __construct()
    {
        $this->_config = Mage::getSingleton('ogone/config');
        return $this;
    }

    /**
     * Return ogone config instance
     *
     * @return Mage_Ogone_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Return debug flag by storeConfig
     *
     * @param int storeId
     * @return bool
     */
    public function getDebug($storeId=null)
    {
        return $this->getConfig()->getConfigData('debug_flag', $storeId);
    }

    /**
     * Flag witch prevent automatic invoice creation
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Redirect url to ogone submit form
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('ogone/api/placeform', array('_secure' => true));
    }

    /**
     * Return payment_action value from config area
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getConfig()->getConfigData('payment_action');
    }

    /**
     * Rrepare params array to send it to gateway page via POST
     *
     * @param Mage_Sales_Model_Order
     * @return array
     */
    public function getFormFields($order)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }
        $billingAddress = $order->getBillingAddress();
        $formFields = array();
        $formFields['PSPID']    = $this->getConfig()->getPSPID();
        $formFields['orderID']  = $order->getIncrementId();
        $formFields['amount']   = round($order->getBaseGrandTotal()*100);
        $formFields['currency'] = Mage::app()->getStore()->getBaseCurrencyCode();
        $formFields['language'] = Mage::app()->getLocale()->getLocaleCode();

        $formFields['CN']       = $this->_translate($billingAddress->getFirstname().' '.$billingAddress->getLastname());
        $formFields['EMAIL']    = $order->getCustomerEmail();
        $formFields['ownerZIP'] = $billingAddress->getPostcode();
        $formFields['ownercty'] = $billingAddress->getCountry();
        $formFields['ownertown']= $this->_translate($billingAddress->getCity());
        $formFields['COM']      = $this->_translate($this->_getOrderDescription($order));
        $formFields['ownertelno']   = $billingAddress->getTelephone();
        $formFields['owneraddress'] =  $this->_translate(str_replace("\n", ' ',$billingAddress->getStreet(-1)));

        $paymentAction = $this->_getOgonePaymentOperation();
        if ($paymentAction ) {
            $formFields['operation'] = $paymentAction;
        }

        $secretCode = $this->getConfig()->getShaOutCode();
        $secretSet  = $formFields['orderID'] . $formFields['amount'] . $formFields['currency'] .
            $formFields['PSPID'] . $paymentAction . $secretCode;

        $formFields['SHASign']  = Mage::helper('ogone')->shaCrypt($secretSet);

        $formFields['homeurl']          = $this->getConfig()->getHomeUrl();
        $formFields['catalogurl']       = $this->getConfig()->getHomeUrl();
        $formFields['accepturl']        = $this->getConfig()->getAcceptUrl();
        $formFields['declineurl']       = $this->getConfig()->getDeclineUrl();
        $formFields['excteptionurl']    = $this->getConfig()->getExceptionUrl();
        $formFields['cancelurl']        = $this->getConfig()->getCancelUrl();

        if ($this->getConfig()->getConfigData('template')=='ogone') {
            $formFields['TP']= '';
            $formFields['PMListType'] = $this->getConfig()->getConfigData('pmlist');
        } else {
            $formFields['TP']= $this->getConfig()->getPayPageTemplate();
        }
        $formFields['TITLE']            = $this->_translate($this->getConfig()->getConfigData('html_title'));
        $formFields['BGCOLOR']          = $this->getConfig()->getConfigData('bgcolor');
        $formFields['TXTCOLOR']         = $this->getConfig()->getConfigData('txtcolor');
        $formFields['TBLBGCOLOR']       = $this->getConfig()->getConfigData('tblbgcolor');
        $formFields['TBLTXTCOLOR']      = $this->getConfig()->getConfigData('tbltxtcolor');
        $formFields['BUTTONBGCOLOR']    = $this->getConfig()->getConfigData('buttonbgcolor');
        $formFields['BUTTONTXTCOLOR']   = $this->getConfig()->getConfigData('buttontxtcolor');
        $formFields['FONTTYPE']         = $this->getConfig()->getConfigData('fonttype');
        $formFields['LOGO']             = $this->getConfig()->getConfigData('logo');
        return $formFields;
    }

    /**
     * to translate UTF 8 to ISO 8859-1
     * Ogone system is only compatible with iso-8859-1 and does not (yet) fully support the utf-8
     */
    protected function _translate($text)
    {
        return htmlentities(iconv("UTF-8", "ISO-8859-1", $text));
    }

    /**
     * Get Ogone Payment Action value
     *
     * @param string
     * @return string
     */
    protected function _getOgonePaymentOperation()
    {
        $value = $this->getPaymentAction();
        if ($value==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            $value = Mage_Ogone_Model_Api::OGONE_AUTHORIZE_ACTION;
        } elseif ($value==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE) {
            $value = Mage_Ogone_Model_Api::OGONE_AUTHORIZE_CAPTURE_ACTION;
        }
        return $value;
    }

    /**
     * get formated order description
     *
     * @param Mage_Sales_Model_Order
     * @return string
     */
    protected function _getOrderDescription($order)
    {
        $invoiceDesc = '';
        $lengs = 0;
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if (Mage::helper('core/string')->strlen($invoiceDesc.$item->getName()) > 10000) {
                break;
            }
            $invoiceDesc .= $item->getName() . ', ';
        }
        return Mage::helper('core/string')->substr($invoiceDesc, 0, -2);
    }
}
