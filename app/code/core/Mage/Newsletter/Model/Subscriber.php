<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Subscriber model
 *
 * @package    Mage_Newsletter
 *
 * @method Mage_Newsletter_Model_Resource_Subscriber _getResource()
 * @method string getChangeStatusAt()
 * @method string getCheckCode()
 * @method Mage_Newsletter_Model_Resource_Subscriber_Collection getCollection()
 * @method int getCustomerId()
 * @method bool getImportMode()
 * @method string getName()
 * @method Mage_Newsletter_Model_Resource_Subscriber getResource()
 * @method Mage_Newsletter_Model_Resource_Subscriber_Collection getResourceCollection()
 * @method int getStoreId()
 * @method string getSubscriberConfirmCode()
 * @method string getSubscriberEmail()
 * @method int getSubscriberId()
 * @method int getSubscriberStatus()
 * @method bool hasCheckCode()
 * @method bool hasCustomerFirstname()
 * @method bool hasCustomerLastname()
 * @method $this setChangeStatusAt(string $value)
 * @method $this setCheckCode(string $value)
 * @method $this setCustomerId(int $value)
 * @method setImportMode(bool $value)
 * @method $this setStoreId(int $value)
 * @method $this setSubscriberConfirmCode(string $value)
 * @method $this setSubscriberEmail(string $value)
 * @method $this setSubscriberId(int $value)
 * @method $this setSubscriberStatus(int $value)
 */
class Mage_Newsletter_Model_Subscriber extends Mage_Core_Model_Abstract
{
    public const STATUS_SUBSCRIBED     = 1;

    public const STATUS_NOT_ACTIVE     = 2;

    public const STATUS_UNSUBSCRIBED   = 3;

    public const STATUS_UNCONFIRMED    = 4;

    public const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'newsletter/subscription/confirm_email_template';

    public const XML_PATH_CONFIRM_EMAIL_IDENTITY       = 'newsletter/subscription/confirm_email_identity';

    public const XML_PATH_SUCCESS_EMAIL_TEMPLATE       = 'newsletter/subscription/success_email_template';

    public const XML_PATH_SUCCESS_EMAIL_IDENTITY       = 'newsletter/subscription/success_email_identity';

    public const XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE   = 'newsletter/subscription/un_email_template';

    public const XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY   = 'newsletter/subscription/un_email_identity';

    public const XML_PATH_CONFIRMATION_FLAG            = 'newsletter/subscription/confirm';

    public const XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG   = 'newsletter/subscription/allow_guest_subscribe';

    /**
     * @deprecated since 1.4.0.1
     */
    public const XML_PATH_SENDING_SET_RETURN_PATH      = Mage_Core_Model_Email_Template::XML_PATH_SENDING_SET_RETURN_PATH;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'newsletter_subscriber';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'subscriber';

    /**
     * True if data changed
     *
     * @var bool
     */
    protected $_isStatusChanged = false;

    protected function _construct()
    {
        $this->_init('newsletter/subscriber');
    }

    /**
     * Alias for getSubscriberId()
     *
     * @return int
     */
    public function getId()
    {
        return $this->getSubscriberId();
    }

    /**
     * Alias for setSubscriberId()
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setSubscriberId($value);
    }

    /**
     * Alias for getSubscriberConfirmCode()
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getSubscriberConfirmCode();
    }

    /**
     * Return link for confirmation of subscription
     *
     * @return string
     */
    public function getConfirmationLink()
    {
        return Mage::helper('newsletter')->getConfirmationUrl($this);
    }

    /**
     * Returns Insubscribe url
     *
     * @return string
     */
    public function getUnsubscriptionLink()
    {
        return Mage::helper('newsletter')->getUnsubscribeUrl($this);
    }

    /**
     * Alias for setSubscriberConfirmCode()
     *
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->setSubscriberConfirmCode($value);
    }

    /**
     * Alias for getSubscriberStatus()
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getSubscriberStatus();
    }

    /**
     * Alias for setSubscriberStatus()
     *
     * @param int $value
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->setSubscriberStatus($value);
    }

    /**
     * Set the error messages scope for subscription
     *
     * @param string $scope
     * @return $this
     */

    public function setMessagesScope($scope)
    {
        $this->getResource()->setMessagesScope($scope);
        return $this;
    }

    /**
     * Alias for getSubscriberEmail()
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getSubscriberEmail();
    }

    /**
     * Alias for setSubscriberEmail()
     *
     * @param string $value
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->setSubscriberEmail($value);
    }

    /**
     * Set for status change flag
     *
     * @param bool $value
     * @return $this
     */
    public function setIsStatusChanged($value)
    {
        $this->_isStatusChanged = (bool) $value;
        return $this;
    }

    /**
     * Return status change flag value
     *
     * @return bool
     */
    public function getIsStatusChanged()
    {
        return $this->_isStatusChanged;
    }

    /**
     * Return customer subscription status
     *
     * @return bool
     */
    public function isSubscribed()
    {
        if ($this->getId() && $this->getStatus() == self::STATUS_SUBSCRIBED) {
            return true;
        }

        return false;
    }

    /**
     * Load subscriber data from resource model by email
     *
     * @param string $subscriberEmail
     * @return $this
     */
    public function loadByEmail($subscriberEmail)
    {
        $this->addData($this->getResource()->loadByEmail($subscriberEmail));
        return $this;
    }

    /**
     * Load subscriber info by customer
     *
     * @return $this
     */
    public function loadByCustomer(Mage_Customer_Model_Customer $customer)
    {
        $data = $this->getResource()->loadByCustomer($customer);
        $this->addData($data);
        if (!empty($data) && $customer->getId() && !$this->getCustomerId()) {
            $this->setCustomerId($customer->getId());
            $this->setSubscriberConfirmCode($this->randomSequence());
            if ($this->getStatus() == self::STATUS_NOT_ACTIVE) {
                $this->setStatus($customer->getIsSubscribed() ? self::STATUS_SUBSCRIBED : self::STATUS_UNSUBSCRIBED);
            }

            $this->save();
        }

        return $this;
    }

    /**
     * Returns sting of random chars
     *
     * @param int $length
     * @return string
     */
    public function randomSequence($length = 32)
    {
        $id = '';
        $par = [];
        $char = array_merge(range('a', 'z'), range(0, 9));
        $charLen = count($char) - 1;
        for ($i = 0; $i < $length; $i++) {
            $disc = mt_rand(0, $charLen);
            $par[$i] = $char[$disc];
            $id .= $char[$disc];
        }

        return $id;
    }

    /**
     * Subscribes by email
     *
     * @param string $email
     * @return int
     * @throws Exception
     */
    public function subscribe($email)
    {
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed   = Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1;
        $isOwnSubscribes = false;
        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        $isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true) {
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                } else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }

            $this->setSubscriberEmail($email);
        } elseif ($this->getStatus() == self::STATUS_SUBSCRIBED) {
            Mage::throwException(Mage::helper('newsletter')->__('This email address is already registered.'));
        }

        if ($isSubscribeOwnEmail) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        $this->save();
        if ($isConfirmNeed === true
            && $isOwnSubscribes === false
        ) {
            $this->sendConfirmationRequestEmail();
        } else {
            $this->sendConfirmationSuccessEmail();
        }

        return $this->getStatus();
    }

    /**
     * Unsubscribes loaded subscription
     */
    public function unsubscribe()
    {
        if ($this->hasCheckCode() && $this->getCode() != $this->getCheckCode()) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid subscription confirmation code.'));
        }

        $this->setSubscriberStatus(self::STATUS_UNSUBSCRIBED)
            ->save();
        $this->sendUnsubscriptionEmail();
        return $this;
    }

    /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  $this
     */
    public function subscribeCustomer($customer)
    {
        $this->loadByCustomer($customer);

        if ($customer->getImportMode()) {
            $this->setImportMode(true);
        }

        if (!$customer->getIsSubscribed() && !$this->getId()) {
            // If subscription flag not set or customer is not a subscriber
            // and no subscribe below
            return $this;
        }

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        /**
         * Logical mismatch between customer registration confirmation code and customer password confirmation
         */
        $confirmation = null;
        if ($customer->isConfirmationRequired() && ($customer->getConfirmation() != $customer->getPassword())) {
            $confirmation = $customer->getConfirmation();
        }

        $sendInformationEmail = false;
        if ($customer->hasIsSubscribed()) {
            $status = $customer->getIsSubscribed()
                ? (!is_null($confirmation) ? self::STATUS_UNCONFIRMED : self::STATUS_SUBSCRIBED)
                : self::STATUS_UNSUBSCRIBED;
            /**
             * If subscription status has been changed then send email to the customer
             */
            if ($status != self::STATUS_UNCONFIRMED && $status != $this->getStatus()) {
                $sendInformationEmail = true;
            }
        } elseif (($this->getStatus() == self::STATUS_UNCONFIRMED) && (is_null($confirmation))) {
            $status = self::STATUS_SUBSCRIBED;
            $sendInformationEmail = true;
        } else {
            $status = ($this->getStatus() == self::STATUS_NOT_ACTIVE ? self::STATUS_UNSUBSCRIBED : $this->getStatus());
        }

        if ($status != $this->getStatus()) {
            $this->setIsStatusChanged(true);
        }

        $this->setStatus($status);

        if (!$this->getId()) {
            $storeId = $customer->getStoreId();
            if ($customer->getStoreId() == 0) {
                $storeId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            }

            $this->setStoreId($storeId)
                ->setCustomerId($customer->getId())
                ->setEmail($customer->getEmail());
        } else {
            $this->setStoreId($customer->getStoreId())
                ->setEmail($customer->getEmail());
        }

        $this->save();
        $sendSubscription = $customer->getData('sendSubscription') || $sendInformationEmail;
        if (is_null($sendSubscription) xor $sendSubscription) {
            if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();
            } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                $this->sendConfirmationSuccessEmail();
            }
        }

        return $this;
    }

    /**
     * Confirms subscriber newsletter
     *
     * @param string $code
     * @return bool
     */
    public function confirm($code)
    {
        if ($this->getCode() == $code) {
            $this->setStatus(self::STATUS_SUBSCRIBED)
                ->setIsStatusChanged(true)
                ->save();
            return true;
        }

        return false;
    }

    /**
     * Mark receiving subscriber of queue newsletter
     *
     * @return $this
     */
    public function received(Mage_Newsletter_Model_Queue $queue)
    {
        $this->getResource()->received($this, $queue);
        return $this;
    }

    /**
     * Sends out confirmation email
     *
     * @return $this
     */
    public function sendConfirmationRequestEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if (!Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_TEMPLATE)
            || !Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /** @var Mage_Core_Model_Translate $translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('core/email_template');
        $email->setDesignConfig(['area' => 'frontend', 'store' => $this->getStoreId()]);

        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            ['subscriber' => $this],
        );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Sends out confirmation success email
     *
     * @return $this
     */
    public function sendConfirmationSuccessEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if (!Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE)
            || !Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /** @var Mage_Core_Model_Translate $translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('core/email_template');
        $email->setDesignConfig(['area' => 'frontend', 'store' => $this->getStoreId()]);

        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            ['subscriber' => $this],
        );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Sends out unsubsciption email
     *
     * @return $this
     */
    public function sendUnsubscriptionEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if (!Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE)
            || !Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /** @var Mage_Core_Model_Translate $translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('core/email_template');
        $email->setDesignConfig(['area' => 'frontend', 'store' => $this->getStoreId()]);

        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            ['subscriber' => $this],
        );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Retrieve Subscribers Full Name if it was set
     *
     * @return null|string
     */
    public function getSubscriberFullName()
    {
        $name = null;
        if ($this->hasCustomerFirstname() || $this->hasCustomerLastname()) {
            $name = Mage::helper('customer')->getFullCustomerName($this);
        }

        return $name;
    }
}
