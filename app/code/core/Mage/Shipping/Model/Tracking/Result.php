<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Tracking_Result
{
    protected $_trackings = [];
    protected $_error = null;

    /**
     * Reset tracking
     * @return $this
     */
    public function reset()
    {
        $this->_trackings = [];
        return $this;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->_error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Add a tracking to the result
     * @param Mage_Shipping_Model_Tracking_Result_Abstract|Mage_Shipping_Model_Rate_Result $result
     * @return $this
     */
    public function append($result)
    {
        if ($result instanceof Mage_Shipping_Model_Tracking_Result_Abstract) {
            $this->_trackings[] = $result;
        } elseif ($result instanceof Mage_Shipping_Model_Rate_Result) {
            $trackings = $result->getAllTrackings();
            foreach ($trackings as $track) {
                $this->append($track);
            }
        }
        return $this;
    }

    /**
     * Return all trackings in the result
     */
    public function getAllTrackings()
    {
        return $this->_trackings;
    }
}
