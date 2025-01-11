<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report statistics admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_StatisticsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'report/statistics';

    /**
     * Admin session model
     *
     * @var null|Mage_Admin_Model_Session
     */
    protected $_adminSession = null;

    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act) {
            $act = 'default';
        }

        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Statistics'), Mage::helper('reports')->__('Statistics'));
        return $this;
    }

    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter', ''));
        $requestData = $this->_filterDates($requestData, ['from', 'to']);
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Retrieve array of collection names by code specified in request
     *
     * @return array
     * @deprecated after 1.4.0.1
     */
    protected function _getCollectionNames()
    {
        $codes = $this->getRequest()->getParam('code');
        if (!$codes) {
            throw new Exception(Mage::helper('adminhtml')->__('No report code specified.'));
        }

        if (!is_array($codes) && !str_contains($codes, ',')) {
            $codes = [$codes];
        } elseif (!is_array($codes)) {
            $codes = explode(',', $codes);
        }

        $aliases = [
            'sales'       => 'sales/report_order',
            'tax'         => 'tax/report_tax',
            'shipping'    => 'sales/report_shipping',
            'invoiced'    => 'sales/report_invoiced',
            'refunded'    => 'sales/report_refunded',
            'coupons'     => 'salesrule/report_rule',
            'bestsellers' => 'sales/report_bestsellers',
            'viewed'      => 'reports/report_product_viewed',
        ];
        $out = [];
        foreach ($codes as $code) {
            $out[] = $aliases[$code];
        }
        return $out;
    }

    /**
     * Refresh statistics for last 25 hours
     *
     * @return $this
     */
    public function refreshRecentAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            $currentDate = Mage::app()->getLocale()->date();
            $date = $currentDate->subHour(25);
            foreach ($collectionsNames as $collectionName) {
                Mage::getResourceModel($collectionName)->aggregate($date);
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Recent statistics have been updated.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to refresh recent statistics.'));
            Mage::logException($e);
        }

        if ($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('*/*');
        } else {
            $this->_redirectReferer('*/*');
        }
        return $this;
    }

    /**
     * Refresh statistics for all period
     *
     * @return $this
     */
    public function refreshLifetimeAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            foreach ($collectionsNames as $collectionName) {
                Mage::getResourceModel($collectionName)->aggregate();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Lifetime statistics have been updated.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to refresh lifetime statistics.'));
            Mage::logException($e);
        }

        if ($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('*/*');
        } else {
            $this->_redirectReferer('*/*');
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Refresh Statistics'));

        $this->_initAction()
            ->_setActiveMenu('report/statistics/refreshstatistics')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Refresh Statistics'), Mage::helper('adminhtml')->__('Refresh Statistics'))
            ->renderLayout();
    }

    /**
     * Retrieve admin session model
     *
     * @return Mage_Admin_Model_Session
     */
    protected function _getSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('admin/session');
        }
        return $this->_adminSession;
    }
}
