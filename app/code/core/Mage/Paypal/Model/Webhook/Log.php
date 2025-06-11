<?php
class Mage_Paypal_Model_Webhook_Log extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('paypal/webhook_log');
    }

    /**
     * Clean old log entries
     *
     * @param int $days Number of days to keep
     * @return $this
     */
    public function clean($days = 30)
    {
        $this->getResource()->clean($days);
        return $this;
    }
} 