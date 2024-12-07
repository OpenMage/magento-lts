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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     */
    public const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/index.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'lastOrders',
            $this->getLayout()->createBlock('adminhtml/dashboard_orders_grid')
        );

        $this->setChild(
            'totals',
            $this->getLayout()->createBlock('adminhtml/dashboard_totals')
        );

        $this->setChild(
            'sales',
            $this->getLayout()->createBlock('adminhtml/dashboard_sales')
        );

        $this->setChild(
            'lastSearches',
            $this->getLayout()->createBlock('adminhtml/dashboard_searches_last')
        );

        $this->setChild(
            'topSearches',
            $this->getLayout()->createBlock('adminhtml/dashboard_searches_top')
        );

        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('adminhtml/dashboard_diagrams');
        } else {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', ['section' => 'admin']));
        }
        $this->setChild('diagrams', $block);

        $this->setChild(
            'grids',
            $this->getLayout()->createBlock('adminhtml/dashboard_grids')
        );

        return parent::_prepareLayout();
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', ['_current' => true, 'period' => null]);
    }
}
