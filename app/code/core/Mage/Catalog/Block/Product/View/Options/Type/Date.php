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


/**
 * Product options text type block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Options_Type_Date
    extends Mage_Catalog_Block_Product_View_Options_Abstract
{
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return parent::_prepareLayout();
    }

    public function getCalendarHtml()
    {
        $require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $calendar = $this->getLayout()
            ->createBlock('core/html_date')
            ->setId('options_'.$this->getOption()->getId().'_date')
            ->setName('options['.$this->getOption()->getId().'][date]')
            ->setClass('input-text'.$require)
            ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));

        return $calendar->getHtml();
    }

    public function getTimeHtml()
    {
        $require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $hour = $this->getLayout()
            ->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'options_'.$this->getOption()->getId().'_hour',
                    'class' => 'select'.$require
                ))
            ->setName('options['.$this->getOption()->getId().'][hour]')
            ->setOptions(array('00' => '00', '01' => '01'));

        $minutes = $this->getLayout()
            ->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'options_'.$this->getOption()->getId().'_minutes',
                    'class' => 'select'.$require
                ))
            ->setName('options['.$this->getOption()->getId().'][minutes]')
            ->setOptions(array('00' => '00', '05' => '05'));

        $timeFormat = $this->getLayout()
            ->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'options_'.$this->getOption()->getId().'_time_format',
                    'class' => 'select'.$require
                ))
            ->setName('options['.$this->getOption()->getId().'][time_format]')
            ->setOptions(array('am' => 'AM', 'pm' => 'PM'));

//Zend_Debug::dump(Mage::app()->getLocale()->getTimeFormat());
//Zend_Debug::dump(Mage::app()->getLocale()->getTimeStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        return $hour->getHtml().$minutes->getHtml().$timeFormat->getHtml();
    }
}