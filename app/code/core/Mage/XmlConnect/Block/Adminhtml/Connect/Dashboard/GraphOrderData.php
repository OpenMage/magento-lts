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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Orders chart data xml renderer block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphOrderData
    extends Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphDataAbstract
{
    /**
     * Initialize object
     */
    public function __construct()
    {
        $this->setHtmlId('orders');
        parent::__construct();
    }

    /**
     * Prepare chart data
     *
     * @return null
     */
    protected function _prepareData()
    {
        $this->setDataHelperName('xmlconnect/adminhtml_dashboard_order');
        $this->setDataRows('quantity');
        $this->_axisMaps = array('x' => 'range', 'y' => 'quantity');
        parent::_prepareData();
    }

    /**
     * Add order chart data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphOrderData
     */
    public function addOrderChartDataToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $this->_xmlObj = $xmlObj->addCustomChild('chart_data_details', null, array('id' => 'orders'));
        $this->_addAllStoreData();
        return $this;
    }
}
