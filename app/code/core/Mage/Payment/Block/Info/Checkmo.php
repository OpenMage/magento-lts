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
class Mage_Payment_Block_Info_Checkmo extends Mage_Payment_Block_Info
{
    protected $_payableTo;

    protected $_mailingAddress;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/checkmo.phtml');
    }

    /**
     * @return string
     */
    public function getPayableTo()
    {
        if (is_null($this->_payableTo)) {
            $this->_convertAdditionalData();
        }

        return $this->_payableTo;
    }

    /**
     * @return string
     */
    public function getMailingAddress()
    {
        if (is_null($this->_mailingAddress)) {
            $this->_convertAdditionalData();
        }

        return $this->_mailingAddress;
    }

    /**
     * @return $this
     */
    protected function _convertAdditionalData()
    {
        $details = false;
        try {
            $details = Mage::helper('core/unserializeArray')
                ->unserialize($this->getInfo()->getAdditionalData());
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        if (is_array($details)) {
            $this->_payableTo = isset($details['payable_to']) ? (string) $details['payable_to'] : '';
            $this->_mailingAddress = isset($details['mailing_address']) ? (string) $details['mailing_address'] : '';
        } else {
            $this->_payableTo = '';
            $this->_mailingAddress = '';
        }

        return $this;
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/checkmo.phtml');
        return $this->toHtml();
    }
}
