<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Method_Checkmo extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'checkmo';

    protected $_formBlockType = 'payment/form_checkmo';

    protected $_infoBlockType = 'payment/info_checkmo';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Method_Checkmo
     */
    public function assignData($data)
    {
        $details = [];
        if ($this->getPayableTo()) {
            $details['payable_to'] = $this->getPayableTo();
        }

        if ($this->getMailingAddress()) {
            $details['mailing_address'] = $this->getMailingAddress();
        }

        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPayableTo()
    {
        return $this->getConfigData('payable_to');
    }

    /**
     * @return string
     */
    public function getMailingAddress()
    {
        return $this->getConfigData('mailing_address');
    }
}
