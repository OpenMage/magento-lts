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
 * iDEAL Advanced Api Acquirer Transaction Request Model
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Ideal_Model_Api_Advanced_AcquirerTrxRequest extends Mage_Ideal_Model_Api_Advanced_Request
{
    /* fields for request xml message
    var $issuerId (mandatory)
    var $merchantReturnUrl (mandatory)
    var $purchaseId (mandatory)
    var $amount (mandatory)
    var $currency (mandatory)
    var $expirationPeriod (mandatory)
    var $language (mandatory)
    var $description (optional)
    var $entranceCode (mandatory)
    */

    function clear() {
        parent::clear();
        $this->unsIssuerId();
        $this->unsMerchantReturnUrl();
        $this->unsPurchaseId();
        $this->unsAmount();
        $this->unsCurrency();
        $this->unsExpirationPeriod();
        $this->unsLanguage();
        $this->unsDescription();
        $this->unsEntranceCode();
    }

    /**
     * this method checks, whether all mandatory properties were set.
     * If done so, true is returned, otherwise false.
     * @return If done so, true is returned, otherwise false.
     */
    function checkMandatory () {
        if ((parent::checkMandatory() == true)
            && (strlen($this->getIssuerId()) > 0)
            && (strlen($this->getMerchantReturnUrl()) > 0)
            && (strlen($this->getPurchaseID()) > 0)
            && (strlen($this->getAmount()) > 0)
            && (strlen($this->getCurrency()) > 0)
            && (strlen($this->getExpirationPeriod()) > 0)
            && (strlen($this->getLanguage()) > 0)
            && (strlen($this->getEntranceCode()) > 0)
            && (strlen($this->getDescription()) > 0)
            ) {
            return true;
        } else {
            return false;
        }
    }

}
