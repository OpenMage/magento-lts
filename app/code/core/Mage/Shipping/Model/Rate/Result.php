<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Rate_Result
{
    /**
     * Shippin method rates
     *
     * @var array
     */
    protected $_rates = [];

    /**
     * Shipping errors
     *
     * @var null|bool
     */
    protected $_error = null;

    /**
     * Reset result
     *
     * @return $this
     */
    public function reset()
    {
        $this->_rates = [];
        return $this;
    }

    /**
     * Set Error
     *
     * @param bool $error
     */
    public function setError($error)
    {
        $this->_error = $error;
    }

    /**
     * Get Error
     *
     * @return null|bool
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Add a rate to the result
     *
     * @param Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Rate_Result_Abstract|Mage_Shipping_Model_Rate_Result_Error $result
     * @return $this
     */
    public function append($result)
    {
        if ($result instanceof Mage_Shipping_Model_Rate_Result_Error) {
            $this->setError(true);
        }

        if ($result instanceof Mage_Shipping_Model_Rate_Result_Abstract) {
            $this->_rates[] = $result;
        } elseif ($result instanceof Mage_Shipping_Model_Rate_Result) {
            $rates = $result->getAllRates();
            foreach ($rates as $rate) {
                $this->append($rate);
            }
        }

        return $this;
    }

    /**
     * Return all quotes in the result
     *
     * @return array
     */
    public function getAllRates()
    {
        return $this->_rates;
    }

    /**
     * Return rate by id in array
     *
     * @param int $id
     * @return null|Mage_Shipping_Model_Rate_Result_Method
     */
    public function getRateById($id)
    {
        return $this->_rates[$id] ?? null;
    }

    /**
     * Return quotes for specified type
     *
     * @param string $carrier
     * @return array
     */
    public function getRatesByCarrier($carrier)
    {
        $result = [];
        foreach ($this->_rates as $rate) {
            if ($rate->getCarrier() === $carrier) {
                $result[] = $rate;
            }
        }

        return $result;
    }

    /**
     * Converts object to array
     *
     * @return array
     */
    public function asArray()
    {
        $currencyFilter = Mage::app()->getStore()->getPriceFilter();
        $rates = [];
        $allRates = $this->getAllRates();
        foreach ($allRates as $rate) {
            $rates[$rate->getCarrier()]['title'] = $rate->getCarrierTitle();
            $rates[$rate->getCarrier()]['methods'][$rate->getMethod()] = [
                'title' => $rate->getMethodTitle(),
                'price' => $rate->getPrice(),
                'price_formatted' => $currencyFilter->filter($rate->getPrice()),
            ];
        }

        return $rates;
    }

    /**
     * Get cheapest rate
     *
     * @return null|Mage_Shipping_Model_Rate_Result_Method
     */
    public function getCheapestRate()
    {
        $cheapest = null;
        $minPrice = 100000;
        foreach ($this->getAllRates() as $rate) {
            if (is_numeric($rate->getPrice()) && $rate->getPrice() < $minPrice) {
                $cheapest = $rate;
                $minPrice = $rate->getPrice();
            }
        }

        return $cheapest;
    }

    /**
     * Sort rates by price from min to max
     *
     * @return $this
     */
    public function sortRatesByPrice()
    {
        if (!is_array($this->_rates) || $this->_rates === []) {
            return $this;
        }

        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        foreach ($this->_rates as $i => $rate) {
            $tmp[$i] = $rate->getPrice();
        }

        natsort($tmp);

        foreach (array_keys($tmp) as $i) {
            $result[] = $this->_rates[$i];
        }

        $this->reset();
        $this->_rates = $result;
        return $this;
    }

    /**
     * Set price for each rate according to count of packages
     *
     * @param int $packageCount
     * @return $this
     */
    public function updateRatePrice($packageCount)
    {
        if ($packageCount > 1) {
            foreach ($this->_rates as $rate) {
                $rate->setPrice($rate->getPrice() * $packageCount);
            }
        }

        return $this;
    }
}
