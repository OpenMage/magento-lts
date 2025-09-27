<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * Class Mage_Shipping_Model_Carrier_Tablerate
 *
 * @package    Mage_Shipping
 * @SuppressWarnings("PHPMD.CamelCasePropertyName")
 */
class Mage_Shipping_Model_Carrier_Tablerate extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * code name
     *
     * @var string
     */
    protected $_code = 'tablerate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Default condition name
     *
     * @var string
     */
    protected $_default_condition_name = 'package_weight';

    /**
     * Condition names
     *
     * @var array
     */
    protected $_conditionNames = [];

    public function __construct()
    {
        parent::__construct();
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }
    }

    /**
     * Collect and get rates
     *
     * @return false|Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        $freePackageValue = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }

        if ($freePackageValue) {
            $request->setPackageValue($request->getPackageValue() - $freePackageValue);
        }
        if (!$request->getConditionName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionName($conditionName ? $conditionName : $this->_default_condition_name);
        }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = $this->_getModel('shipping/rate_result');
        $rate = $this->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        if (!empty($rate) && $rate['price'] >= 0) {
            /** @var Mage_Shipping_Model_Rate_Result_Method $method */
            $method = $this->_getModel('shipping/rate_result_method');

            $method->setCarrier('tablerate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('bestway');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty)) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            }

            $method->setPrice($shippingPrice);
            $method->setCost($rate['cost']);

            $result->append($method);
        } elseif (empty($rate) && $request->getFreeShipping() === true) {
            /**
             * was applied promotion rule for whole cart
             * other shipping methods could be switched off at all
             * we must show table rate method with 0$ price, if grand_total more, than min table condition_value
             * free setPackageWeight() has already was taken into account
             */
            $request->setPackageValue($freePackageValue);
            $request->setPackageQty($freeQty);
            $rate = $this->getRate($request);
            if (!empty($rate) && $rate['price'] >= 0) {
                /** @var Mage_Shipping_Model_Rate_Result_Method $method */
                $method = $this->_getModel('shipping/rate_result_method');

                $method->setCarrier('tablerate');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('bestway');
                $method->setMethodTitle($this->getConfigData('name'));

                $method->setPrice(0);
                $method->setCost(0);

                $result->append($method);
            }
        } else {
            /** @var Mage_Shipping_Model_Rate_Result_Error $error */
            $error = $this->_getModel('shipping/rate_result_error');
            $error->setCarrier('tablerate');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }

        return $result;
    }

    /**
     * Get Model
     *
     * @param string $modelName
     * @return Mage_Core_Model_Abstract
     */
    protected function _getModel($modelName)
    {
        return Mage::getModel($modelName);
    }

    /**
     * Get Rate
     *
     * @return array|bool
     */
    public function getRate(Mage_Shipping_Model_Rate_Request $request)
    {
        return Mage::getResourceModel('shipping/carrier_tablerate')->getRate($request);
    }

    /**
     * Get code
     *
     * @param string $type
     * @param string $code
     * @return array|string
     */
    public function getCode($type, $code = '')
    {
        $codes = [

            'condition_name' => [
                'package_weight' => Mage::helper('shipping')->__('Weight vs. Destination'),
                'package_value' => Mage::helper('shipping')->__('Price vs. Destination'),
                'package_qty' => Mage::helper('shipping')->__('# of Items vs. Destination'),
            ],

            'condition_name_short' => [
                'package_weight' => Mage::helper('shipping')->__('Weight (and above)'),
                'package_value' => Mage::helper('shipping')->__('Order Subtotal (and above)'),
                'package_qty' => Mage::helper('shipping')->__('# of Items (and above)'),
            ],

        ];

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Table Rate code type: %s', $type));
        }

        if ($code === '') {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Table Rate code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['bestway' => $this->getConfigData('name')];
    }
}
