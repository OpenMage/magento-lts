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
 * Admin abstract reports controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Controller_Report_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * Admin session model
     *
     * @var null|Mage_Admin_Model_Session
     */
    protected $_adminSession = null;

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

    /**
     * Add report breadcrumbs
     *
     * @return Mage_Adminhtml_Controller_Report_Abstract
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'));
        return $this;
    }

    /**
     * Report action init operations
     *
     * @param array|Varien_Object $blocks
     * @return Mage_Adminhtml_Controller_Report_Abstract
     */
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
     * Add refresh statistics links
     *
     * @param string $flagCode
     * @param string $refreshCode
     * @return Mage_Adminhtml_Controller_Report_Abstract
     */
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {
        $flag = Mage::getModel('reports/flag')->setReportFlagCode($flagCode)->loadSelf();
        $updatedAt = ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0,
                new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT),
                true
            )
            : 'undefined';

        $refreshStatsLink = $this->getUrl('*/report_statistics');
        $directRefreshLink = $this->getUrl('*/report_statistics/refreshRecent', ['code' => $refreshCode]);

        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Last updated: %s. To refresh last day\'s <a href="%s">statistics</a>, click <a href="%s">here</a>.', $updatedAt, $refreshStatsLink, $directRefreshLink));
        return $this;
    }
}
