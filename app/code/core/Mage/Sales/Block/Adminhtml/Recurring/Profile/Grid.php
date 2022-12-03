<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set ajax/session parameters
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_recurring_profile_grid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/recurring_profile_collection');
        $this->setCollection($collection);
        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('profile_id', 'desc');
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $profile = Mage::getModel('sales/recurring_profile');

        $this->addColumn('reference_id', [
            'header' => $profile->getFieldLabel('reference_id'),
            'index' => 'reference_id',
            'html_decorators' => ['nobr'],
            'width' => 1,
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'     => Mage::helper('adminhtml')->__('Store'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ]);
        }

        $this->addColumn('state', [
            'header' => $profile->getFieldLabel('state'),
            'index' => 'state',
            'type'  => 'options',
            'options' => $profile->getAllStates(),
            'html_decorators' => ['nobr'],
            'width' => 1,
        ]);

        $this->addColumn('created_at', [
            'header' => $profile->getFieldLabel('created_at'),
            'index' => 'created_at',
            'type' => 'datetime',
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('updated_at', [
            'header' => $profile->getFieldLabel('updated_at'),
            'index' => 'updated_at',
            'type' => 'datetime',
            'html_decorators' => ['nobr'],
        ]);

        $methods = [];
        foreach (Mage::helper('payment')->getRecurringProfileMethods() as $method) {
            $methods[$method->getCode()] = $method->getTitle();
        }
        $this->addColumn('method_code', [
            'header'  => $profile->getFieldLabel('method_code'),
            'index'   => 'method_code',
            'type'    => 'options',
            'options' => $methods,
        ]);

        $this->addColumn('schedule_description', [
            'header' => $profile->getFieldLabel('schedule_description'),
            'index' => 'schedule_description',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_recurring_profile/view', ['profile' => $row->getId()]);
    }

    /**
     * Return grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
