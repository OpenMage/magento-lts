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

    public function __construct($message = '', $debugData = [])
    {
        $this->_debugData = $debugData;
        parent::__construct($message);
    }

    /**
     * Get debug data
     */
    public function getDebugData(): array
    {
        return $this->_debugData;
    }
}
