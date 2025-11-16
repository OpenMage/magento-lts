<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core session model
 *
 * @package    Mage_Core
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method string getCurrencyCode()
 * @method int getJustVotedPoll()
 * @method $this setCookieShouldBeReceived(bool $value)
 * @method $this setCurrencyCode(string $value)
 * @method $this setFormData(array $value)
 * @method $this setJustVotedPoll(int $value)
 * @method $this setLastUrl(string $value)
 * @method $this setOrderIds(array $value)
 * @method $this unsCookieShouldBeReceived()
 * @method $this unsSessionHosts()
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
     * @param null|string $formKey
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
