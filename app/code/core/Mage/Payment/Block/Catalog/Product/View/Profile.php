<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profile info/options product view block
 *
 * @category   Mage
 * @package    Mage_Payment
 *
 * @method $this setDateHtmlId(string $string)
 */
class Mage_Payment_Block_Catalog_Product_View_Profile extends Mage_Core_Block_Template
{
    /**
     * Recurring profile instance
     *
     * @var Mage_Payment_Model_Recurring_Profile
     */
    protected $_profile = false;

    /**
     * Getter for schedule info
     * array(
     *     <title> => array('blah-blah', 'bla-bla-blah', ...)
     *     <title2> => ...
     * )
     * @return array
     */
    public function getScheduleInfo()
    {
        $scheduleInfo = [];
        foreach ($this->_profile->exportScheduleInfo() as $info) {
            $scheduleInfo[$info->getTitle()] = $info->getSchedule();
        }
        return $scheduleInfo;
    }

    /**
     * Render date input element
     *
     * @return string
     */
    public function getDateHtml()
    {
        if ($this->_profile->getStartDateIsEditable()) {
            $this->setDateHtmlId('recurring_start_date');
            $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $calendar = $this->getLayout()
                ->createBlock('core/html_date')
                ->setId('recurring_start_date')
                ->setName(Mage_Payment_Model_Recurring_Profile::BUY_REQUEST_START_DATETIME)
                ->setClass('datetime-picker input-text')
                ->setImage($this->getSkinUrl('images/calendar.gif'))
                ->setFormat($format)
                ->setTime(true);
            return $calendar->getHtml();
        }
        return '';
    }

    /**
     * Determine current product and initialize its recurring profile model
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $product = Mage::registry('current_product');
        if ($product) {
            $this->_profile = Mage::getModel('payment/recurring_profile')->importProduct($product);
        }
        return parent::_prepareLayout();
    }

    /**
     * If there is no profile information, the template will be unset, blocking the output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_profile) {
            $this->_template = '';
        }
        return parent::_toHtml();
    }
}
