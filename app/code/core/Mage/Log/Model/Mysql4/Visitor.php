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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Visitor log resource
 *
 * @category   Mage
 * @package    Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Mysql4_Visitor extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('log/visitor', 'visitor_id');
    }

    protected function _prepareDataForSave(Mage_Core_Model_Abstract $visitor)
    {
        return array(
            'session_id'    => $visitor->getSessionId(),
            'first_visit_at'=> $visitor->getFirstVisitAt(),
            'last_visit_at' => $visitor->getLastVisitAt(),
            'last_url_id'   => $visitor->getLastUrlId() ? $visitor->getLastUrlId() : 0,
            'store_id'      => Mage::app()->getStore()->getId(),
        );
    }

    /**
     * Saving information about url
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveUrlInfo($visitor)
    {
        $this->_getWriteAdapter()->insert($this->getTable('log/url_info_table'), array(
            'url'    => Mage::helper('core/string')->substr($visitor->getUrl(), 0, 250),
            'referer'=> Mage::helper('core/string')->substr($visitor->getHttpReferer(), 0, 250)
        ));
        $visitor->setLastUrlId($this->_getWriteAdapter()->lastInsertId());
        return $this;
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $visitor)
    {
        if (!$visitor->getIsNewVisitor()) {
            $this->_saveUrlInfo($visitor);
        }
        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $visitor)
    {
        if ($visitor->getIsNewVisitor()) {
            $this->_saveVisitorInfo($visitor);
            $visitor->setIsNewVisitor(false);
        }
        else {
            $this->_saveVisitorUrl($visitor);
            if ($visitor->getDoCustomerLogin() || $visitor->getDoCustomerLogout()) {
                $this->_saveCustomerInfo($visitor);
            }
            if ($visitor->getDoQuoteCreate() || $visitor->getDoQuoteDestroy()) {
                $this->_saveQuoteInfo($visitor);
            }
        }
        return $this;
    }

    /**
     * Saving visitor information
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
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

        $write = $this->_getWriteAdapter();
        $data = array(
            'visitor_id'            => $visitor->getId(),
            'http_referer'          => $stringHelper->substr($visitor->getHttpReferer(), 0, 255),
            'http_user_agent'       => $stringHelper->substr($visitor->getHttpUserAgent(), 0, 255),
            'http_accept_charset'   => $stringHelper->substr($visitor->getHttpAcceptCharset(), 0, 255),
            'http_accept_language'  => $stringHelper->substr($visitor->getHttpAcceptLanguage(), 0, 255),
            'server_addr'           => $visitor->getServerAddr(),
            'remote_addr'           => $visitor->getRemoteAddr(),
        );

        $write->insert($this->getTable('log/visitor_info'), $data);
        return $this;
    }

    /**
     * Saving visitor and url relation
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveVisitorUrl($visitor)
    {
        $write = $this->_getWriteAdapter();
        $write->insert($this->getTable('log/url_table'), array(
            'url_id'    => $visitor->getLastUrlId(),
            'visitor_id'=> $visitor->getId(),
            'visit_time'=> now(),
        ));
        return $this;
    }

    /**
     * Saving information about customer
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveCustomerInfo($visitor)
    {
        $write = $this->_getWriteAdapter();

        if ($visitor->getDoCustomerLogin()) {
            $write->insert($this->getTable('log/customer'), array(
                'visitor_id'    => $visitor->getVisitorId(),
                'customer_id'   => $visitor->getCustomerId(),
                'login_at'      => now(),
                'store_id'      => Mage::app()->getStore()->getId(),
            ));
            $visitor->setCustomerLogId($write->lastInsertId());
            $visitor->setDoCustomerLogin(false);
        }

        if ($visitor->getDoCustomerLogout() && $logId = $visitor->getCustomerLogId()) {
            $write->update($this->getTable('log/customer'),
                array(
                    'logout_at' => now(),
                    'store_id'  => Mage::app()->getStore()->getId(),
                ),
                $write->quoteInto('log_id=?', $logId)
            );
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
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveQuoteInfo($visitor)
    {
        $write = $this->_getWriteAdapter();
        if ($visitor->getDoQuoteCreate()) {
            $write->insert($this->getTable('log/quote_table'), array(
                'quote_id'  => $visitor->getQuoteId(),
                'visitor_id'=> $visitor->getId(),
                'created_at'=> now()
            ));
            $visitor->setDoQuoteCreate(false);
        }

        if ($visitor->getDoQuoteDestroy()) {
            /**
             * We have delete quote from log because if original quote was
             * deleted and Mysql restarted we will get key duplication error
             */
            $write->delete($this->getTable('log/quote_table'),
                $write->quoteInto('quote_id=?', $visitor->getQuoteId())
            );
//            $write->update($this->getTable('log/quote_table'),
//                array('deleted_at'=> now()),
//                $write->quoteInto('quote_id=?', $visitor->getQuoteId())
//            );
            $visitor->setDoQuoteDestroy(false);
            $visitor->setQuoteId(null);
        }
        return $this;
    }
}
