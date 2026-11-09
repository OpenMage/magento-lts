<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

declare(strict_types=1);

/**
 * Display-only method for order payments whose original implementation is unavailable.
 */
class Mage_Payment_Model_Method_Unavailable extends Mage_Payment_Model_Method_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_canUseInternal = false;
        $this->_canUseCheckout = false;
        $this->_canUseForMultishipping = false;
        $this->_canManageRecurringProfiles = false;
    }

    #[Override]
    public function getCode(): string
    {
        return (string) $this->getInfoInstance()->getMethod();
    }

    #[Override]
    public function getTitle(): string
    {
        return $this->getCode();
    }

    #[Override]
    public function canEdit(): bool
    {
        return false;
    }

    #[Override]
    public function isAvailable($quote = null): bool
    {
        return false;
    }

    #[Override]
    public function validate(): never
    {
        Mage::throwException(Mage::helper('payment')->__('The requested Payment Method is not available.'));
    }
}
