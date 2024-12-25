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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core session model
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method $this setCookieShouldBeReceived(bool $value)
 * @method $this unsCookieShouldBeReceived()
 * @method $this unsSessionHosts()
 * @method string getCurrencyCode()
 * @method $this setCurrencyCode(string $value)
 * @method $this setFormData(array $value)
 * @method int getJustVotedPoll()
 * @method $this setOrderIds(array $value)
 * @method $this setJustVotedPoll(int $value)
 * @method $this setLastUrl(string $value)
 */
class Mage_Core_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $name = $data['name'] ?? null;
        $this->init('core', $name);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->renewFormKey();
        }
        return $this->getData('_form_key');
    }

    /**
     * Creates new Form key
     */
    public function renewFormKey()
    {
        $this->setData('_form_key', Mage::helper('core')->getRandomString(16));
    }

    /**
     * Validates Form key
     *
     * @param string|null $formKey
     * @return bool
     */
    public function validateFormKey($formKey)
    {
        return ($formKey === $this->getFormKey());
    }

    public function getOrderIds(bool $clear = false): array
    {
        return $this->getData('order_ids', $clear) ?? [];
    }
}
