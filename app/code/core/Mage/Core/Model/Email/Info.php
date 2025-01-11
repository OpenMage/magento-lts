<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Email information model
 * Email message may contain addresses in any of these three fields:
 *  -To:  Primary recipients
 *  -Cc:  Carbon copy to secondary recipients and other interested parties
 *  -Bcc: Blind carbon copy to tertiary recipients who receive the message
 *        without anyone else (including the To, Cc, and Bcc recipients) seeing who the tertiary recipients are
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Email_Info extends Varien_Object
{
    /**
     * Name list of "Bcc" recipients
     *
     * @var array
     */
    protected $_bccNames = [];

    /**
     * Email list of "Bcc" recipients
     *
     * @var array
     */
    protected $_bccEmails = [];

    /**
     * Name list of "To" recipients
     *
     * @var array
     */
    protected $_toNames = [];

    /**
     * Email list of "To" recipients
     *
     * @var array
     */
    protected $_toEmails = [];

    /**
     * Add new "Bcc" recipient to current email
     *
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addBcc($email, $name = null)
    {
        $this->_bccNames[] = $name;
        $this->_bccEmails[] = $email;
        return $this;
    }

    /**
     * Add new "To" recipient to current email
     *
     * @param array|string $email
     * @param array|string|null $name
     * @return $this
     */
    public function addTo($email, $name = null)
    {
        $this->_toNames[] = $name;
        $this->_toEmails[] = $email;
        return $this;
    }

    /**
     * Get the name list of "Bcc" recipients
     *
     * @return array
     */
    public function getBccNames()
    {
        return $this->_bccNames;
    }

    /**
     * Get the email list of "Bcc" recipients
     *
     * @return array
     */
    public function getBccEmails()
    {
        return $this->_bccEmails;
    }

    /**
     * Get the name list of "To" recipients
     *
     * @return array
     */
    public function getToNames()
    {
        return $this->_toNames;
    }

    /**
     * Get the email list of "To" recipients
     *
     * @return array
     */
    public function getToEmails()
    {
        return $this->_toEmails;
    }
}
