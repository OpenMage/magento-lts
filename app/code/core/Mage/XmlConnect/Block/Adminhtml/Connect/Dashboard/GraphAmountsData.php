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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Amounts chart data xml renderer block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphAmountsData
    extends Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphDataAbstract
{
    /**
     * Initialize object
     */
    public function __construct()
    {
        $this->setHtmlId('amounts');
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
        $this->setDataRows('revenue');
        $this->_axisMaps = array('x' => 'range', 'y' => 'revenue');
        parent::_prepareData();
    }

    /**
     * Add order chart data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphAmountsData
     */
    public function addAmountsChartDataToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $this->_xmlObj = $xmlObj->addCustomChild('chart_data_details', null, array('id' => 'amounts'));
        $this->_addAllStoreData();
        return $this;
    }
}
