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
 * iDEAL Advanced Api Directory Response Model
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Ideal_Model_Api_Advanced_DirectoryResponse extends Mage_Ideal_Model_Api_Advanced_Response {
    /*
    var $acquirerId
    var $issuerList - array of Issuer Objects
    */

    /**
     * adds an Issuer to the IssuerList
     */
    function addIssuer($issuer) {
        if(is_a($issuer, "Mage_Ideal_Api_Advanced_Issuer")) {
            $this->setIssuerList(array_merge((array)$this->getIssuerList(), (array)$issuer));
        }
    }
}

?>
