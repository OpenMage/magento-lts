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
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Roles grid block
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Roles_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct grid block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('rolesGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Varien_Db_Select::SQL_DESC);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Api2_Model_Resource_Acl_Global_Role_Collection */
        $collection = Mage::getModel('api2/acl_global_role')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('oauth')->__('ID'),
            'index'  => 'entity_id',
            'align'  => 'right',
            'width'  => '50px',
        ));

        $this->addColumn('role_name', array(
            'header' => Mage::helper('oauth')->__('Role Name'),
            'index'  => 'role_name',
            'escape' => true,
        ));

        $this->addColumn('tole_user_type', array(
            'header'         => Mage::helper('oauth')->__('User Type'),
            'sortable'       => false,
            'frame_callback' => array($this, 'decorateUserType')
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('oauth')->__('Created At'),
            'index'  => 'created_at'
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Get row URL
     *
     * @param Mage_Api2_Model_Acl_Global_Role $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');

        if ($session->isAllowed('system/api/roles/edit')) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
        return null;
    }

    /**
     * Decorate 'User Type' column
     *
     * @param string $renderedValue Rendered value
     * @param Mage_Api2_Model_Acl_Global_Role $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateUserType($renderedValue, $row, $column, $isExport)
    {
        switch ($row->getEntityId()) {
            case Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID:
                $userType = Mage::helper('api2')->__('Guest');
                break;
            case Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID:
                $userType = Mage::helper('api2')->__('Customer');
                break;
            default:
                $userType = Mage::helper('api2')->__('Admin');
                break;
        }
        return $userType;
    }
}
