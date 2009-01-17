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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_locale;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/index.phtml');

    }

    protected function _prepareLayout()
    {
        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
                ->setTemplate('dashboard/store/switcher.phtml')
        );

        $this->setChild('lastOrders',
                $this->getLayout()->createBlock('adminhtml/dashboard_orders_grid')
        );

        $this->setChild('totals',
                $this->getLayout()->createBlock('adminhtml/dashboard_totals')
        );

        $this->setChild('sales',
                $this->getLayout()->createBlock('adminhtml/dashboard_sales')
        );

        $this->setChild('lastSearches',
                $this->getLayout()->createBlock('adminhtml/dashboard_searches_last')
        );

        $this->setChild('topSearches',
                $this->getLayout()->createBlock('adminhtml/dashboard_searches_top')
        );

        $this->setChild('diagrams',
                $this->getLayout()->createBlock('adminhtml/dashboard_diagrams')
        );

        $this->setChild('grids',
                $this->getLayout()->createBlock('adminhtml/dashboard_grids')
        );

        parent::_prepareLayout();
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current'=>true, 'period'=>null));
    }
}
