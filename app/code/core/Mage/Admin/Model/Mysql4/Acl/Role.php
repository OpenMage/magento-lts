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
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ACL role resource
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Mysql4_Acl_Role
{
    protected $_roleTable;
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $this->_roleTable = Mage::getSingleton('core/resource')->getTableName('admin/role');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('admin_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('admin_write');
    }
    
    public function load($roleId)
    {
        $select = $this->_read->select()->from($this->_roleTable)
            ->where("role_id=?", $roleId);
        return $this->_read->fetchRow($select);
    }

    public function save(Mage_Admin_Model_Acl_Role $role)
    {
        $data = $role->getData();
        
        $this->_write->beginTransaction();

        try {
            if ($role->getId()) {
                $condition = $this->_write->quoteInto('role_id=?', $role->getRoleId());
                $this->_write->update($this->_roleTable, $data, $condition);
            } else { 
                $data['created'] = now();
                $this->_write->insert($this->_roleTable, $data);
                $role->setRoleId($this->_write->lastInsertId());
            }

            $this->_write->commit();
        }
        catch (Mage_Core_Exception $e)
        {
            $this->_write->rollback();
            throw $e;
        }
        
        return $role;
    }
    
    public function delete()
    {
            
    }
}