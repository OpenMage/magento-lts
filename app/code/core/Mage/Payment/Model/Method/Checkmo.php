<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
