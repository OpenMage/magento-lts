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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml billing agreements grid
 *
 * @author Magento Core Team <core@magentocommerce.com>
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
        return $this->getUrl('*/sales_billing_agreement/grid', array('_current' => true));
    }

    /**
     * Retrieve row url
     *
     * @param Mage_Sales_Model_Billing_Agreement $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/sales_billing_agreement/view', array('agreement' => $item->getAgreementId()));
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
        $this->addColumn('agreement_id', array(
            'header'            => Mage::helper('sales')->__('ID'),
            'index'             => 'agreement_id',
            'type'              => 'text'
        ));

        $this->addColumn('customer_email', array(
            'header'            => Mage::helper('sales')->__('Customer Email'),
            'index'             => 'customer_email',
            'type'              => 'text',
            'escape'            => true
        ));

        $this->addColumn('customer_firstname', array(
            'header'            => Mage::helper('sales')->__('Customer Name'),
            'index'             => 'customer_firstname',
            'type'              => 'text',
            'escape'            => true
        ));

        $this->addColumn('customer_middlename', array(
            'header'            => Mage::helper('sales')->__('Customer Middle Name'),
            'index'             => 'customer_middlename',
            'type'              => 'text',
            'escape'            => true
        ));

        $this->addColumn('customer_lastname', array(
            'header'            => Mage::helper('sales')->__('Customer Last Name'),
            'index'             => 'customer_lastname',
            'type'              => 'text',
            'escape'            => true
        ));

        $this->addColumn('method_code', array(
            'header'            => Mage::helper('sales')->__('Payment Method'),
            'index'             => 'method_code',
            'type'              => 'options',
            'options'           => Mage::helper('payment')->getAllBillingAgreementMethods()
        ));

        $this->addColumn('reference_id', array(
            'header'            => Mage::helper('sales')->__('Reference ID'),
            'index'             => 'reference_id',
            'type'              => 'text'
        ));

        $this->addColumn('status', array(
            'header'            => Mage::helper('sales')->__('Status'),
            'index'             => 'status',
            'type'              => 'options',
            'options'           => Mage::getSingleton('sales/billing_agreement')->getStatusesArray()
        ));

        $this->addColumn('created_at', array(
            'header'            => Mage::helper('sales')->__('Created At'),
            'index'             => 'agreement_created_at',
            'width'             => 1,
            'type'              => 'datetime',
            'align'             => 'center',
            'default'           => $this->__('N/A'),
            'html_decorators'   => array('nobr')
        ));

        $this->addColumn('updated_at', array(
            'header'            => Mage::helper('sales')->__('Updated At'),
            'index'             => 'agreement_updated_at',
            'width'             => 1,
            'type'              => 'datetime',
            'align'             => 'center',
            'default'           => $this->__('N/A'),
            'html_decorators'   => array('nobr')
        ));

        return parent::_prepareColumns();
    }
}
