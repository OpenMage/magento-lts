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
 * Adminhtml dashboard orders diagram
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Orders extends Mage_Adminhtml_Block_Dashboard_Graph
{
    protected $_axisMaps = [
        'x' => 'range',
        'y' => 'quantity',
    ];

    public function __construct()
    {
        $this->setHtmlId('orders');
        parent::__construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->setDataHelperName('adminhtml/dashboard_order');

        /** @var Mage_Adminhtml_Helper_Dashboard_Order $dataHelper */
        $dataHelper = $this->getDataHelper();

        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $this->getRequest();

        $dataHelper->setParam('store', $request->getParam('store'));
        $dataHelper->setParam('website', $request->getParam('website'));
        $dataHelper->setParam('group', $request->getParam('group'));

        $this->setDataRows('quantity');

        parent::_prepareData();
    }
}
