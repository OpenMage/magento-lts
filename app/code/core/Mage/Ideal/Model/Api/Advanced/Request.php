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
 * iDEAL Advanced Api Request Model
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Ideal_Model_Api_Advanced_Request extends Varien_Object
{
    /**
     * clears all parameters
     */
    public function clear() {
        $this->unsMerchantId();
        $this->unsSubId();
        $this->unsAuthentication();
    }

    /**
     * this method checks, whether all mandatory properties are set.
     * @return true if all fields are valid, otherwise returns false
     */
     function checkMandatory () {
        if (strlen($this->getMerchantId()) > 0
            && strlen($this->getSubID()) > 0
            && strlen($this->getAuthentication()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param authentication The type of authentication to set.
     * Currently only "RSA_SHA1" is implemented. (mandatory)
     */
    function setAuthentication($authentication) {
        $this->setData('authentication', trim($authentication));
    }

    /**
     * @param merchantID The merchantID to set. (mandatory)
     */
    function setMerchantId($merchantID) {
        $this->setData('merchant_id', trim($merchantID));
    }

    /**
     * @param subID The subID to set. (mandatory)
     */
    function setSubId($subID) {
        $this->setData('sub_id', trim($subID));
    }
}
