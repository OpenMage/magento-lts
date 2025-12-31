<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Persistent
 */

use Carbon\Carbon;

/**
 * Persistent Session Model
 *
 * @package    Mage_Persistent
 *
 * @method Mage_Persistent_Model_Resource_Session _getResource()
 * @method int                                    getCustomerId()
 * @method string                                 getInfo()
 * @method string                                 getKey()
 * @method Mage_Persistent_Model_Resource_Session getResource()
 * @method $this                                  setCustomerId(int $value)
 * @method $this                                  setInfo(string $value)
 * @method $this                                  setKey(string $value)
 * @method $this                                  setWebsiteId(null|int|string $value)
 */
class Mage_Persistent_Model_Session extends Mage_Core_Model_Abstract
{
    public const KEY_LENGTH = 50;

    public const COOKIE_NAME = 'persistent_shopping_cart';

    /**
     * Fields which model does not save into `info` db field
     *
     * @var array
     */
    protected $_unserializableFields = ['persistent_id', 'key', 'customer_id', 'website_id', 'info', 'updated_at'];

    /**
     * If model loads expired sessions
     *
     * @var bool
     */
    protected $_loadExpired = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('persistent/session');
    }

    /**
     * Set if load expired persistent session
     *
     * @param  bool  $loadExpired
     * @return $this
     */
    public function setLoadExpired($loadExpired = true)
    {
        $this->_loadExpired = $loadExpired;
        return $this;
    }

    /**
     * Get if model loads expired sessions
     *
     * @return bool
     */
    public function getLoadExpired()
    {
        return $this->_loadExpired;
    }

    /**
     * Get date-time before which persistent session is expired
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return string
     */
    public function getExpiredBefore($store = null)
    {
        return gmdate(
            Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT,
            Carbon::now()->getTimestamp() - Mage::helper('persistent')->getLifeTime($store),
        );
    }

    /**
     * Serialize info for Resource Model to save
     * For new model check and set available cookie key
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Setting info
        $info = [];
        foreach ($this->getData() as $index => $value) {
            if (!in_array($index, $this->_unserializableFields)) {
                $info[$index] = $value;
            }
        }

        $this->setInfo(Mage::helper('core')->jsonEncode($info));

        if ($this->isObjectNew()) {
            $this->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            // Setting cookie key
            do {
                $this->setKey(Mage::helper('core')->getRandomString(self::KEY_LENGTH));
            } while (!$this->getResource()->isKeyAllowed($this->getKey()));
        }

        return $this;
    }

    /**
     * Set model data from info field
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $info = Mage::helper('core')->jsonDecode($this->getInfo());
        if (is_array($info)) {
            foreach ($info as $key => $value) {
                $this->setData($key, $value);
            }
        }

        return $this;
    }

    /**
     * Get persistent session by cookie key
     *
     * @param  string $key
     * @return $this
     */
    public function loadByCookieKey($key = null)
    {
        if (is_null($key)) {
            $key = Mage::getSingleton('core/cookie')->get(self::COOKIE_NAME);
        }

        if ($key) {
            $this->load($key, 'key');
        }

        return $this;
    }

    /**
     * Load session model by specified customer id
     *
     * @param  int                      $id
     * @return Mage_Core_Model_Abstract
     */
    public function loadByCustomerId($id)
    {
        return $this->load($id, 'customer_id');
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param  int   $customerId
     * @param  bool  $clearCookie
     * @return $this
     */
    public function deleteByCustomerId($customerId, $clearCookie = true)
    {
        if ($clearCookie) {
            $this->removePersistentCookie();
        }

        $this->getResource()->deleteByCustomerId($customerId);
        return $this;
    }

    /**
     * Remove persistent cookie
     *
     * @return $this
     */
    public function removePersistentCookie()
    {
        Mage::getSingleton('core/cookie')->delete(self::COOKIE_NAME);
        return $this;
    }

    /**
     * Delete expired persistent sessions for the website
     *
     * @param  null|int $websiteId
     * @return $this
     */
    public function deleteExpired($websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }

        $lifetime = Mage::getConfig()->getNode(
            Mage_Persistent_Helper_Data::XML_PATH_LIFE_TIME,
            'website',
            (int) $websiteId,
        );

        if ($lifetime) {
            $this->getResource()->deleteExpired(
                $websiteId,
                gmdate(Varien_Date::DATETIME_PHP_FORMAT, Carbon::now()->getTimestamp() - $lifetime),
            );
        }

        return $this;
    }

    /**
     * Delete 'persistent' cookie
     *
     * @inheritDoc
     */
    protected function _afterDeleteCommit()
    {
        Mage::getSingleton('core/cookie')->delete(Mage_Persistent_Model_Session::COOKIE_NAME);
        return parent::_afterDeleteCommit();
    }

    /**
     * Set `updated_at` to be always changed
     *
     * @inheritDoc
     */
    public function save()
    {
        $this->setUpdatedAt(gmdate(Varien_Date::DATETIME_PHP_FORMAT));
        return parent::save();
    }
}
