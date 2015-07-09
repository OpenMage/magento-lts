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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Visitor log resource
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Resource_Visitor extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store condition object that know should we log something or not
     *
     * @var Mage_Log_Helper_Data
     */
    protected $_urlLoggingCondition;

    public function __construct(array $data = array())
    {
        parent::__construct();
        $this->_urlLoggingCondition = isset($data['log_condition'])
            ? $data['log_condition'] : Mage::helper('log');
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('log/visitor', 'visitor_id');
    }

    /**
     * Prepare data for save
     *
     * @param Mage_Core_Model_Abstract $visitor
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $visitor)
    {
        return array(
            'session_id'        => $visitor->getSessionId(),
            'first_visit_at'    => $visitor->getFirstVisitAt(),
            'last_visit_at'     => $visitor->getLastVisitAt(),
            'last_url_id'       => $visitor->getLastUrlId() ? $visitor->getLastUrlId() : 0,
            'store_id'          => Mage::app()->getStore()->getId(),
        );
    }

    /**
     * Saving information about url
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Resource_Visitor
     */
    protected function _saveUrlInfo($visitor)
    {
        $adapter    = $this->_getWriteAdapter();
        $data       = new Varien_Object(array(
            'url'    => Mage::helper('core/string')->substr($visitor->getUrl(), 0, 250),
            'referer'=> Mage::helper('core/string')->substr($visitor->getHttpReferer(), 0, 250)
        ));
        $bind = $this->_prepareDataForTable($data, $this->getTable('log/url_info_table'));

        $adapter->insert($this->getTable('log/url_info_table'), $bind);

        $visitor->setLastUrlId($adapter->lastInsertId($this->getTable('log/url_info_table')));

        return $this;
    }

    /**
     * Save url info before save
     *
     * @param Mage_Core_Model_Abstract $visitor
     * @return Mage_Log_Model_Resource_Visitor
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $visitor)
    {
        if (!$this->_urlLoggingCondition->isLogEnabled()) {
            return $this;
        }
        if (!$visitor->getIsNewVisitor()) {
            $this->_saveUrlInfo($visitor);
        }
        return $this;
    }

    /**
     * Actions after save
     *
     * @param Mage_Core_Model_Abstract $visitor
     * @return Mage_Log_Model_Resource_Visitor
     */
    protected function _afterSave(Mage_Core_Model_Abstract $visitor)
    {
        if ($this->_urlLoggingCondition->isLogDisabled()) {
            return $this;
        }
        if ($visitor->getIsNewVisitor()) {
            if ($this->_urlLoggingCondition->isLogEnabled()) {
                $this->_saveVisitorInfo($visitor);
                $visitor->setIsNewVisitor(false);
            }
        } else {
            if ($this->_urlLoggingCondition->isLogEnabled()) {
                $this->_saveVisitorUrl($visitor);
                if ($visitor->getDoCustomerLogin() || $visitor->getDoCustomerLogout()) {
                    $this->_saveCustomerInfo($visitor);
                }
            }
            if ($this->_urlLoggingCondition->isVisitorLogEnabled()) {
                if ($visitor->getDoQuoteCreate() || $visitor->getDoQuoteDestroy()) {
                    $this->_saveQuoteInfo($visitor);
                }
            }
        }
        return $this;
    }

    /**
     * Perform actions after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);
        if ($this->_urlLoggingCondition->isLogDisabled()) {
            return $this;
        }
        // Add information about quote to visitor
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getTable('log/quote_table'), 'quote_id')
            ->where('visitor_id = ?', $object->getId())->order('quote_id DESC')->limit(1);
        $result = $adapter->query($select)->fetch();
        if (isset($result['quote_id'])) {
            $object->setQuoteId((int) $result['quote_id']);
        }
        return $this;
    }

    /**
     * Saving visitor information
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Resource_Visitor
     */
    protected function _saveVisitorInfo($visitor)
    {
        /* @var $stringHelper Mage_Core_Helper_String */
        $stringHelper = Mage::helper('core/string');

        $referer    = $stringHelper->cleanString($visitor->getHttpReferer());
        $referer    = $stringHelper->substr($referer, 0, 255);
        $userAgent  = $stringHelper->cleanString($visitor->getHttpUserAgent());
        $userAgent  = $stringHelper->substr($userAgent, 0, 255);
        $charset    = $stringHelper->cleanString($visitor->getHttpAcceptCharset());
        $charset    = $stringHelper->substr($charset, 0, 255);
        $language   = $stringHelper->cleanString($visitor->getHttpAcceptLanguage());
        $language   = $stringHelper->substr($language, 0, 255);

        $adapter = $this->_getWriteAdapter();
        $data = new Varien_Object(array(
            'visitor_id'            => $visitor->getId(),
            'http_referer'          => $referer,
            'http_user_agent'       => $userAgent,
            'http_accept_charset'   => $charset,
            'http_accept_language'  => $language,
            'server_addr'           => $visitor->getServerAddr(),
            'remote_addr'           => $visitor->getRemoteAddr(),
        ));
        $bind = $this->_prepareDataForTable($data, $this->getTable('log/visitor_info'));

        $adapter->insert($this->getTable('log/visitor_info'), $bind);
        return $this;
    }

    /**
     * Saving visitor and url relation
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Resource_Visitor
     */
    protected function _saveVisitorUrl($visitor)
    {
        $data = new Varien_Object(array(
            'url_id'        => $visitor->getLastUrlId(),
            'visitor_id'    => $visitor->getId(),
            'visit_time'    => Mage::getSingleton('core/date')->gmtDate()
        ));
        $bind = $this->_prepareDataForTable($data, $this->getTable('log/url_table'));

        $this->_getWriteAdapter()->insert($this->getTable('log/url_table'), $bind);
        return $this;
    }

    /**
     * Saving information about customer
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Resource_Visitor
     */
    protected function _saveCustomerInfo($visitor)
    {
        $adapter = $this->_getWriteAdapter();

        if ($visitor->getDoCustomerLogin()) {
            $data = new Varien_Object(array(
                'visitor_id'    => $visitor->getVisitorId(),
                'customer_id'   => $visitor->getCustomerId(),
                'login_at'      => Mage::getSingleton('core/date')->gmtDate(),
                'store_id'      => Mage::app()->getStore()->getId()
            ));
            $bind = $this->_prepareDataForTable($data, $this->getTable('log/customer'));

            $adapter->insert($this->getTable('log/customer'), $bind);
            $visitor->setCustomerLogId($adapter->lastInsertId($this->getTable('log/customer')));
            $visitor->setDoCustomerLogin(false);
        }

        if ($visitor->getDoCustomerLogout() && $logId = $visitor->getCustomerLogId()) {
            $data = new Varien_Object(array(
                'logout_at' => Mage::getSingleton('core/date')->gmtDate(),
                'store_id'  => (int)Mage::app()->getStore()->getId(),
            ));

            $bind = $this->_prepareDataForTable($data, $this->getTable('log/customer'));

            $condition = array(
                'log_id = ?' => (int) $logId,
            );

            $adapter->update($this->getTable('log/customer'), $bind, $condition);

            $visitor->setDoCustomerLogout(false);
            $visitor->setCustomerId(null);
            $visitor->setCustomerLogId(null);
        }

        return $this;
    }

    /**
     * Saving information about quote
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Resource_Visitor
     */
    protected function _saveQuoteInfo($visitor)
    {
        $adapter = $this->_getWriteAdapter();
        if ($visitor->getDoQuoteCreate()) {
            $data = new Varien_Object(array(
                'quote_id'      => (int) $visitor->getQuoteId(),
                'visitor_id'    => (int) $visitor->getId(),
                'created_at'    => Mage::getSingleton('core/date')->gmtDate()
            ));

            $bind = $this->_prepareDataForTable($data, $this->getTable('log/quote_table'));

            $adapter->insert($this->getTable('log/quote_table'), $bind);

            $visitor->setDoQuoteCreate(false);
        }

        if ($visitor->getDoQuoteDestroy()) {
            /**
             * We have delete quote from log because if original quote was
             * deleted and Mysql restarted we will get key duplication error
             */
            $condition = array(
                'quote_id = ?' => (int) $visitor->getQuoteId(),
            );

            $adapter->delete($this->getTable('log/quote_table'), $condition);

            $visitor->setDoQuoteDestroy(false);
            $visitor->setQuoteId(null);
        }
        return $this;
    }
}
