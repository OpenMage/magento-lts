<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Calculations model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Calculator
{
    /**
     * Delta collected during rounding steps
     *
     * @var float
     */
    protected $_delta = 0.0;

    /**
     * Store instance
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store = null;

    /**
     * Initialize calculator
     *
     * @param Mage_Core_Model_Store|int $store
     */
    public function __construct($store)
    {
        if (!($store instanceof Mage_Core_Model_Store)) {
            $store = Mage::app()->getStore($store);
        }
        $this->_store = $store;
    }

    /**
     * Round price considering delta
     *
     * @param float $price
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function deltaRound($price, $negative = false)
    {
        $roundedPrice = $price;
        if ($roundedPrice) {
            if ($negative) {
                $this->_delta = -$this->_delta;
            }
            $price  += $this->_delta;
            $roundedPrice = $this->_store->roundPrice($price);
            $this->_delta = $price - $roundedPrice;
            if ($negative) {
                $this->_delta = -$this->_delta;
            }
        }
        return $roundedPrice;
    }
}
