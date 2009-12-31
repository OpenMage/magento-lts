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
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract USA shipping carrier model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Abstract extends Mage_Shipping_Model_Carrier_Abstract
{

    const USA_COUNTRY_ID = 'US';
    const PUERTORICO_COUNTRY_ID = 'PR';

    public function getTrackingInfo($tracking)
    {
        $info = array();

        $result = $this->getTracking($tracking);

        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Check if carrier has shipping tracking option available
     * All Mage_Usa carriers have shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    public function isCityRequired()
    {
        return true;
    }

    public function isZipCodeRequired()
    {
        return true;
    }

    /**
     * Processing additional validation to check is carrier applicable.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error|boolean
     */
    public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
        $maxAllowedWeight = (float) $this->getConfigData('max_package_weight');
        $error = null;
        $showMethod = $this->getConfigData('showmethod');
        foreach ($request->getAllItems() as $item) {
            if ($item->getProduct() && $item->getProduct()->getId()) {
                if ($item->getProduct()->getWeight() > $maxAllowedWeight) {
                    $error = Mage::getModel('shipping/rate_result_error');
                    $error->setCarrier($this->_code)
                        ->setCarrierTitle($this->getConfigData('title'));
                    $errorMsg = $this->getConfigData('specificerrmsg');
                    $error->setErrorMessage($errorMsg?$errorMsg:Mage::helper('shipping')->__('The shipping module is not available.'));
                    break;
                }
            }
        }
        if (null !== $error && $showMethod) {
            return $error;
        } elseif (null !== $error) {
            return false;
        }
        return $this;
    }
}
