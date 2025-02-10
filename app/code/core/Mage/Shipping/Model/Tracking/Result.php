<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
