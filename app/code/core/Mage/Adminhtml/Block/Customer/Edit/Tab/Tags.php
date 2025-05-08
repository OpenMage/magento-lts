<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customer orders grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Tags extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('customer')->__('ID'),
            'align'     => 'center',
            'index'     => 'entity_id',
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name',
        ]);
        $this->addColumn('email', [
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => 40,
            'align'     => 'center',
            'index'     => 'email',
        ]);
        $this->addColumn('telephone', [
            'header'    => Mage::helper('customer')->__('Telephone'),
            'align'     => 'center',
            'index'     => 'billing_telephone',
        ]);
        $this->addColumn('billing_postcode', [
            'header'    => Mage::helper('customer')->__('ZIP/Post Code'),
            'index'     => 'billing_postcode',
        ]);
        $this->addColumn('billing_country_id', [
            'header'    => Mage::helper('customer')->__('Country'),
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ]);
        $this->addColumn('customer_since', [
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'date',
            'format'    => 'Y.m.d',
            'index'     => 'created_at',
        ]);
        $this->addColumn('action', [
            'type'      => 'action',
            'align'     => 'center',
            'format'    => '<a href="' . $this->getUrl('*/sales/edit/id/$entity_id') . '">' . Mage::helper('customer')->__('Edit') . '</a>',
            'is_system' => true,
        ]);

        $this->setColumnFilter('entity_id')
            ->setColumnFilter('email')
            ->setColumnFilter('name');

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', ['_current' => true]);
    }
}
