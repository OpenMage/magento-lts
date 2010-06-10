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
 * @category    Mage
 * @package     Mage_Admin
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Admin_Model_Mysql4_Rules extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('admin/rule', 'rule_id');
    }

    /**
     * Save ACL resources
     *
     * @param Mage_Admin_Model_Rules $rule
     */
    public function saveRel(Mage_Admin_Model_Rules $rule)
    {
        try {
            $this->_getWriteAdapter()->beginTransaction();
            $roleId = $rule->getRoleId();
            $this->_getWriteAdapter()->delete($this->getMainTable(), "role_id = {$roleId}");
            $postedResources = $rule->getResources();
            if ($postedResources) {
                $row = array(
                    'role_type'   => 'G',
                    'resource_id' => 'all',
                    'privileges'  => '', // not used yet
                    'assert_id'   => 0,
                    'role_id'     => $roleId,
                    'permission'  => 'allow'
                );

                // If all was selected save it only and nothing else.
                if ($postedResources === array('all')) {
                    $this->_getWriteAdapter()->insert($this->getMainTable(), $row);
                } else {
                    foreach (Mage::getModel('admin/roles')->getResourcesList2D() as $index => $resName) {
                        $row['permission']  = (in_array($resName, $postedResources) ? 'allow' : 'deny');
                        $row['resource_id'] = trim($resName, '/');
                        $this->_getWriteAdapter()->insert($this->getMainTable(), $row);
                    }
                }
            }

            $this->_getWriteAdapter()->commit();
        } catch (Mage_Core_Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        } catch (Exception $e){
            $this->_getWriteAdapter()->rollBack();
            Mage::logException($e);
        }
    }
}
