<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal Exception
 */
class Mage_Paypal_Model_Exception extends Mage_Core_Exception
{
    protected $_debugData = [];

    public function __construct($message = '', $debugData = [], ?Throwable $previous = null)
    {
        $this->_debugData = $debugData;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get debug data
     */
    public function getDebugData(): array
    {
        return $this->_debugData;
    }

    /**
     * Whether PayPal asks the buyer to approve the order again (an error issue, or the order status itself).
     */
    public function isPayerActionRequired(): bool
    {
        if (str_contains($this->getMessage(), 'PAYER_ACTION_REQUIRED')
            || strtoupper((string) ($this->_debugData['status'] ?? '')) === 'PAYER_ACTION_REQUIRED'
        ) {
            return true;
        }

        foreach ((array) ($this->_debugData['details'] ?? []) as $detail) {
            if (is_array($detail) && ($detail['issue'] ?? '') === 'PAYER_ACTION_REQUIRED') {
                return true;
            }
        }

        return false;
    }
}
