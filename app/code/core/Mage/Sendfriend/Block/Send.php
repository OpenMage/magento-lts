<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sendfriend
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Email to a Friend Block
 *
 * @category   Mage
 * @package    Mage_Sendfriend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sendfriend_Block_Send extends Mage_Core_Block_Template
{
    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        $name = $this->getFormData()->getData('sender/name');
        if (!empty($name)) {
            return trim($name);
        }

        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        if ($session->isLoggedIn()) {
            return $session->getCustomer()->getName();
        }

        return '';
    }

    /**
     * Retrieve sender email address
     *
     * @return string
     */
    public function getEmail()
    {
        $email = $this->getFormData()->getData('sender/email');
        if (!empty($email)) {
            return trim($email);
        }

        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        if ($session->isLoggedIn()) {
            return $session->getCustomer()->getEmail();
        }

        return '';
    }

    /**
     * Retrieve Message text
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getFormData()->getData('sender/message');
    }

    /**
     * Retrieve Form data or empty Varien_Object
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (!$data instanceof Varien_Object) {
            $formData = Mage::getSingleton('catalog/session')->getFormData(true);
            $data = new Varien_Object();
            if ($formData) {
                $data->addData($formData);
            }
            $this->setData('form_data', $data);
        }

        return $data;
    }

    /**
     * Set Form data array
     *
     * @param array $data
     * @return $this
     */
    public function setFormData($data)
    {
        if (is_array($data)) {
            $this->setData('form_data', new Varien_Object($data));
        }

        return $this;
    }

    /**
     * Retrieve Current Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id', null);
    }

    /**
     * Retrieve current category id for product
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getRequest()->getParam('cat_id', null);
    }

    /**
     * Retrieve Max Recipients
     *
     * @return int
     */
    public function getMaxRecipients()
    {
        return Mage::helper('sendfriend')->getMaxRecipients();
    }

    /**
     * Retrieve count of recipients
     *
     * @return int
     */
    public function getRecipientsCount()
    {
        $recipientsEmail = $this->getFormData()->getData('recipients/email');
        return (is_array($recipientsEmail)) ? count($recipientsEmail) : 0;
    }

    /**
     * Retrieve Send URL for Form Action
     *
     * @return string
     */
    public function getSendUrl()
    {
        return Mage::getUrl('*/*/sendmail', [
            'id'     => $this->getProductId(),
            'cat_id' => $this->getCategoryId(),
            '_secure' => $this->_isSecure()
        ]);
    }

    /**
     * Return send friend model
     *
     * @return Mage_Sendfriend_Model_Sendfriend
     */
    protected function _getSendfriendModel()
    {
        return Mage::registry('send_to_friend_model');
    }

    /**
     * Check if user is allowed to send
     *
     * @return bool
     */
    public function canSend()
    {
        return !$this->_getSendfriendModel()->isExceedLimit();
    }
}
