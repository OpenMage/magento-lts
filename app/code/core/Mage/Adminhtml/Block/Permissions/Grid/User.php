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

/**
 * Users grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_Grid_User extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('username');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel("permissions/users")->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header'    =>Mage::helper('adminhtml')->__('ID'),
            'width'     =>5,
            'align'     =>'right',
            'sortable'  =>true,
            'index'     =>'user_id'
        ));
        $this->addColumn('username', array(
            'header'    =>Mage::helper('adminhtml')->__('User Name'),
            'index'     =>'username'
        ));
        $this->addColumn('firstname', array(
            'header'    =>Mage::helper('adminhtml')->__('First Name'),
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>Mage::helper('adminhtml')->__('Last Name'),
            'index'     =>'lastname'
        ));
        $this->addColumn('email', array(
            'header'    =>Mage::helper('adminhtml')->__('Email'),
            'width'     =>40,
            'align'     =>'left',
            'index'     =>'email'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edituser', array('id' => $row->getUserId()));
    }

}

