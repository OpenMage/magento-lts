<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * Flat rate shipping model
 *
 * @package    Mage_Shipping
 *
 * @method int getFreeBoxes()
 * @method $this setFreeBoxes(int $value)
 */
class Mage_Shipping_Model_Carrier_Flatrate extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'flatrate';

    protected $_isFixed = true;

    /**
     * @return Mage_Shipping_Model_Rate_Result|false
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }

        $this->setFreeBoxes($freeBoxes);

        $result = Mage::getModel('shipping/rate_result');

        $shippingType = $this->getConfigData('type');
        $shippingPrice = (float) $this->getConfigData('price');
        if ($shippingType == 'I') { // per item
            $shippingPrice = ($request->getPackageQty() * $shippingPrice) - ($this->getFreeBoxes() * $shippingPrice);
        } elseif ($shippingType != 'O') { // not per order
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('flatrate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('flatrate');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['flatrate' => $this->getConfigData('name')];
    }
}
