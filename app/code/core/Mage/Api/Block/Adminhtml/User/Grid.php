<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml permissions user grid
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Block_Adminhtml_User_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Api_Block_Adminhtml_User_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserGrid');
        $this->setDefaultSort('username');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('api/user_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_id', [
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'user_id'
        ]);

        $this->addColumn('username', [
            'header'    => Mage::helper('adminhtml')->__('User Name'),
            'index'     => 'username'
        ]);

        $this->addColumn('firstname', [
            'header'    => Mage::helper('adminhtml')->__('First Name'),
            'index'     => 'firstname'
        ]);

        $this->addColumn('lastname', [
            'header'    => Mage::helper('adminhtml')->__('Last Name'),
            'index'     => 'lastname'
        ]);

        $this->addColumn('email', [
            'header'    => Mage::helper('adminhtml')->__('Email'),
            'width'     => 40,
            'align'     => 'left',
            'index'     => 'email'
        ]);

        $this->addColumn('is_active', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => ['1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Api_Model_User $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['user_id' => $row->getId()]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/roleGrid', []);
    }
}
