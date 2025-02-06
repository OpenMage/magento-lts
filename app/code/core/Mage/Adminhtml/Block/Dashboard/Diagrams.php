<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard diagram tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Diagrams extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('diagram_tab');
        $this->setDestElementId('diagram_tab_content');
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {
        $this->addTab('orders', [
            'label'     => $this->__('Orders'),
            'content'   => $this->getLayout()->createBlock('adminhtml/dashboard_tab_orders')->toHtml(),
            'active'    => true,
        ]);

        $this->addTab('amounts', [
            'label'     => $this->__('Amounts'),
            'content'   => $this->getLayout()->createBlock('adminhtml/dashboard_tab_amounts')->toHtml(),
        ]);
        return parent::_prepareLayout();
    }
}
