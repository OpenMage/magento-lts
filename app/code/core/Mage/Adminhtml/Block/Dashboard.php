<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     *
     * @deprecated
     * @see Mage_Adminhtml_Helper_Dashboard_Data::XML_PATH_ENABLE_CHARTS
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
            $this->getLayout()->createBlock('adminhtml/dashboard_orders_grid'),
        );

        $this->setChild(
            'totals',
            $this->getLayout()->createBlock('adminhtml/dashboard_totals'),
        );

        $this->setChild(
            'sales',
            $this->getLayout()->createBlock('adminhtml/dashboard_sales'),
        );

        $this->setChild(
            'lastSearches',
            $this->getLayout()->createBlock('adminhtml/dashboard_searches_last'),
        );

        $this->setChild(
            'topSearches',
            $this->getLayout()->createBlock('adminhtml/dashboard_searches_top'),
        );

        /** @var Mage_Adminhtml_Helper_Dashboard_Data $helper */
        $helper = Mage::helper('adminhtml/dashboard_data');
        if ($helper->isChartEnabled()) {
            $block = $this->getLayout()->createBlock('adminhtml/dashboard_diagrams');
        } else {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', ['section' => 'admin']));
        }

        $this->setChild('diagrams', $block);

        $this->setChild(
            'grids',
            $this->getLayout()->createBlock('adminhtml/dashboard_grids'),
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
