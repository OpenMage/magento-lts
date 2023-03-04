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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml billing agreements grid
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('billing_agreements');
        $this->setUseAjax(true);
        $this->setDefaultSort('agreement_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/sales_billing_agreement/grid', ['_current' => true]);
    }

    /**
     * Retrieve row url
     *
     * @param Mage_Sales_Model_Billing_Agreement $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/sales_billing_agreement/view', ['agreement' => $item->getAgreementId()]);
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/billing_agreement_collection')
            ->addCustomerDetails();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('agreement_id', [
            'header'            => Mage::helper('sales')->__('ID'),
            'index'             => 'agreement_id',
            'type'              => 'text'
        ]);

        $this->addColumn('customer_email', [
            'header'            => Mage::helper('sales')->__('Customer Email'),
            'index'             => 'customer_email',
            'type'              => 'text',
            'escape'            => true
        ]);

        $this->addColumn('customer_firstname', [
            'header'            => Mage::helper('sales')->__('Customer Name'),
            'index'             => 'customer_firstname',
            'type'              => 'text',
            'escape'            => true
        ]);

        $this->addColumn('customer_middlename', [
            'header'            => Mage::helper('sales')->__('Customer Middle Name'),
            'index'             => 'customer_middlename',
            'type'              => 'text',
            'escape'            => true
        ]);

        $this->addColumn('customer_lastname', [
            'header'            => Mage::helper('sales')->__('Customer Last Name'),
            'index'             => 'customer_lastname',
            'type'              => 'text',
            'escape'            => true
        ]);

        $this->addColumn('method_code', [
            'header'            => Mage::helper('sales')->__('Payment Method'),
            'index'             => 'method_code',
            'type'              => 'options',
            'options'           => Mage::helper('payment')->getAllBillingAgreementMethods()
        ]);

        $this->addColumn('reference_id', [
            'header'            => Mage::helper('sales')->__('Reference ID'),
            'index'             => 'reference_id',
            'type'              => 'text'
        ]);

        $this->addColumn('status', [
            'header'            => Mage::helper('sales')->__('Status'),
            'index'             => 'status',
            'type'              => 'options',
            'options'           => Mage::getSingleton('sales/billing_agreement')->getStatusesArray()
        ]);

        $this->addColumn('created_at', [
            'header'            => Mage::helper('sales')->__('Created At'),
            'index'             => 'agreement_created_at',
            'type'              => 'datetime',
            'align'             => 'center',
            'default'           => $this->__('N/A'),
            'html_decorators'   => ['nobr']
        ]);

        $this->addColumn('updated_at', [
            'header'            => Mage::helper('sales')->__('Updated At'),
            'index'             => 'agreement_updated_at',
            'type'              => 'datetime',
            'align'             => 'center',
            'default'           => $this->__('N/A'),
            'html_decorators'   => ['nobr']
        ]);

        return parent::_prepareColumns();
    }
}
