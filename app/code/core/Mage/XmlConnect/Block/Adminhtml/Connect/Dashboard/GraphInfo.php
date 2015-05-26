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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin application diagram info renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo extends Mage_Core_Block_Abstract
{
    /**
     * Time range filter options
     *
     * @var array
     */
    protected $_timeRangeOptions;

    /**
     * Add last orders info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo
     */
    public function addGraphInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        /** @var $graphInfoXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $graphInfoXmlObj = $xmlObj->addCustomChild('chart');
        $this->_addRangeValues($graphInfoXmlObj)->_addDataSelector($graphInfoXmlObj)->_addTotalsBar($graphInfoXmlObj);

        $graphInfoXmlObj = $graphInfoXmlObj->addCustomChild('chart_data');
        $this->_addChartDataOrders($graphInfoXmlObj)->_addChartDataAmounts($graphInfoXmlObj);
        return $this;
    }

    /**
     * Add time range select filed to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo
     */
    protected function _addRangeValues(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $options = $this->_getRangeOptions();

        /** @var $rangeXmlObj Mage_XmlConnect_Model_Simplexml_Form_Element_Select */
        $rangeXmlObj = Mage::getModel('xmlconnect/simplexml_form_element_select', array(
            'label' => $this->__('Select Range'),
            'options' => $options,
            'value' => $options[0]['value']
        ));
        $rangeXmlObj->setId('range_id');
        $xmlObj->appendChild($rangeXmlObj->toXmlObject());
        return $this;
    }

    /**
     * Get range filter options
     *
     * @return array
     */
    protected function _getRangeOptions()
    {
        if (null === $this->_timeRangeOptions) {
            $options = array();
            foreach ($this->helper('adminhtml/dashboard_data')->getDatePeriods() as $value => $label) {
                if (in_array($value, array('custom'))) {
                    continue;
                }
                $options[] = array('label' => $label, 'value' => $value);
            }
            $this->_timeRangeOptions = $options;
        }
        return $this->_timeRangeOptions;
    }

    /**
     * Add data select field to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo
     */
    protected function _addDataSelector(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        /** @var $rangeXmlObj Mage_XmlConnect_Model_Simplexml_Form_Element_Select */
        $rangeXmlObj = Mage::getModel('xmlconnect/simplexml_form_element_select', array(
            'label' => $this->__('Select Chart'),
            'value' => 'orders',
            'options' => array(
                array('label' => $this->__('Orders'), 'value' => 'orders'),
                array('label' => $this->__('Amounts'), 'value' => 'amounts')
        )));
        $rangeXmlObj->setId('data_details_id');
        $xmlObj->appendChild($rangeXmlObj->toXmlObject());
        return $this;
    }

    /**
     * Add orders chart data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $graphInfoXmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo
     */
    protected function _addChartDataOrders(Mage_XmlConnect_Model_Simplexml_Element $graphInfoXmlObj)
    {
        $this->getChild('graph_order_data')->setRangeOptions($this->_getRangeOptions())
            ->addOrderChartDataToXmlObj($graphInfoXmlObj);
        return $this;
    }

    /**
     * Add amounts chart data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $graphInfoXmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo
     */
    protected function _addChartDataAmounts(Mage_XmlConnect_Model_Simplexml_Element $graphInfoXmlObj)
    {
        $this->getChild('graph_amounts_data')->setRangeOptions($this->_getRangeOptions())
            ->addAmountsChartDataToXmlObj($graphInfoXmlObj);
        return $this;
    }

    /**
     * Add totals chart data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $graphInfoXmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphInfo
     */
    protected function _addTotalsBar(Mage_XmlConnect_Model_Simplexml_Element $graphInfoXmlObj)
    {
        $this->getChild('graph_totals_data')->setRangeOptions($this->_getRangeOptions())
            ->addTotalsDataToXmlObj($graphInfoXmlObj);
        return $this;
    }
}
