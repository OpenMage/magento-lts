<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Catalog_Model_Sendfriend extends Mage_Core_Model_Abstract
{
    protected $_names = array();
    protected $_emails = array();
    protected $_sender = array();
    protected $_ip = 0;
    protected $_product = null;

    protected $_period = 3600; // hour

    protected $_cookieName = 'stf';

    protected function _construct()
    {
        $this->_init('catalog/sendfriend');
    }


    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    public function send()
    {
        $errors = array();

        $this->_emailModel = Mage::getModel('core/email_template');

        $this->_emailModel->load($this->getTemplate());
        if (!$this->_emailModel->getId()) {
            Mage::throwException(
               Mage::helper('catalog')
                   ->__('Invalid transactional email code')
            );
        }

        $this->_emailModel->setSenderName(strip_tags($this->_sender['name']));
        $this->_emailModel->setSenderEmail(strip_tags($this->_sender['email']));

        foreach ($this->_emails as $k=>$email) {
            if (!$this->_sendOne($email, $this->_names[$k])) {
                $errors[] = $email;
            }
        }

        if (count($errors)) {
            Mage::throwException(
                Mage::helper('catalog')
                    ->__('Email to %s was not sent', implode(', ', $errors))
            );
        }
    }

    public function canSend()
    {
        if (!$this->canEmailToFriend()) {
            Mage::throwException(
                Mage::helper('catalog')
                    ->__('You cannot email this product to a friend')
            );
        }

        if ($this->_getSendToFriendCheckType()) {
            $amount = $this->_amountByCookies();
        } else {
            $amount = $this->_amountByIp();
        }

        if ($amount >= $this->getMaxSendsToFriend()){
            Mage::throwException(
                Mage::helper('catalog')
                    ->__('You have exceeded limit of %d sends in an hour', $this->getMaxSendsToFriend())
            );
        }

        $maxRecipients = $this->getMaxRecipients();
        if (count($this->_emails) > $maxRecipients) {
            Mage::throwException(
                Mage::helper('catalog')
                    ->__('You cannot send more than %d emails at a time', $this->getMaxRecipients())
            );
        }

        if (count($this->_emails) < 1) {
            Mage::throwException(
                Mage::helper('catalog')
                    ->__('You have to specify at least one recipient')
            );
        }

        if (!$this->getTemplate()){
            Mage::throwException(
                Mage::helper('catalog')
                    ->__('Email template is not specified by administrator')
            );
        }

        return true;
    }

    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    public function setRecipients($recipients)
    {
        $this->_emails = array_unique($recipients['email']);
        $this->_names = $recipients['name'];
    }

    public function setProduct($product){
        $this->_product = $product;
    }

    public function setSender($sender){
        $this->_sender = $sender;
    }

    public function getSendCount($ip, $startTime)
    {
        $count = $this->_getResource()->getSendCount($this, $ip, $startTime);
        return $count;
    }

    /**
     * Get max allowed uses of "Send to Friend" function per hour
     *
     * @return integer
     */
    public function getMaxSendsToFriend()
    {
        return max(0, (int) Mage::getStoreConfig('sendfriend/email/max_per_hour'));
    }

    /**
     * Get current "Send to friend" template
     *
     * @return string
     */
    public function getTemplate()
    {
        return Mage::getStoreConfig('sendfriend/email/template');
    }

    /**
     * Get max allowed recipients for "Send to a Friend" function
     *
     * @return integer
     */
    public function getMaxRecipients()
    {
        return max(0, (int) Mage::getStoreConfig('sendfriend/email/max_recipients'));
    }

    /**
     * Check if user is allowed to email product to a friend
     *
     * @return boolean
     */
    public function canEmailToFriend()
    {
        if (!Mage::getStoreConfig('sendfriend/email/enabled')) {
            return false;
        }
        if (!Mage::getStoreConfig('sendfriend/email/allow_guest')
            && !Mage::getSingleton('customer/session')->isLoggedIn()) {
            return false;
        }
        return true;
    }

    private function _sendOne($email, $name){
        $email = trim($email);

        $vars = array(
           'senderName' => strip_tags($this->_sender['name']),
           'senderEmail' => strip_tags($this->_sender['email']),
           'receiverName' => strip_tags($name),
           'receiverEmail' => strip_tags($email),
           'product' => $this->_product,
           'message' => strip_tags($this->_sender['message'])
           );

        if (!$this->_emailModel->send(strip_tags($email), strip_tags($name), $vars)){
            return false;
        }

        return true;
    }

    /**
     * Get check type for "Send to Friend" function
     *
     * @return integer
     */
    private function _getSendToFriendCheckType()
    {
        return max(0, (int) Mage::getStoreConfig('sendfriend/email/check_by'));
    }

    private function _amountByCookies()
    {
        $newTimes = array();
        $oldTimes = Mage::getSingleton('core/cookie')->get($this->_cookieName);
        if ($oldTimes){
            $oldTimes = explode(',', $oldTimes);
            foreach ($oldTimes as $time){
                if (is_numeric($time) && $time >= time()-$this->_period){
                    $newTimes[] = $time;
                }
            }
        }
        $amount = count($newTimes);

        $newTimes[] = time();
        Mage::getSingleton('core/cookie')
            ->set($this->_cookieName, implode(',', $newTimes), $this->_period);

        return $amount;
    }

    private function _amountByIp()
    {
        $this->_deleteLogsBefore(time() - $this->_period);

        $amount = $this->getSendCount($this->_ip, time() - $this->_period);

        $this->setData(array('ip'=>$this->_ip, 'time'=>time()));
        $this->save();

        return $amount;
    }

    private function _deleteLogsBefore($time)
    {
        $this->_getResource()->deleteLogsBefore($time);
        return $this;
    }
}