<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Roles grid block
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Roles_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
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
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Api2_Model_Resource_Acl_Global_Role_Collection $collection */
        $collection = Mage::getModel('api2/acl_global_role')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header' => Mage::helper('oauth')->__('ID'),
            'index'  => 'entity_id',
        ]);

        $this->addColumn('role_name', [
            'header' => Mage::helper('oauth')->__('Role Name'),
            'index'  => 'role_name',
            'escape' => true,
        ]);

        $this->addColumn('tole_user_type', [
            'header'         => Mage::helper('oauth')->__('User Type'),
            'sortable'       => false,
            'frame_callback' => [$this, 'decorateUserType'],
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('oauth')->__('Created At'),
            'index'  => 'created_at',
        ]);

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
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * Get row URL
     *
     * @param Mage_Api2_Model_Acl_Global_Role $row
     * @return null|string
     */
    public function getRowUrl($row)
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');

        if ($session->isAllowed('system/api/roles/edit')) {
            return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
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
        return match ($row->getEntityId()) {
            Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID => Mage::helper('api2')->__('Guest'),
            Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID => Mage::helper('api2')->__('Customer'),
            default => Mage::helper('api2')->__('Admin'),
        };
    }
}
