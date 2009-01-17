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
 * @package    Mage_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Api_Model_Mysql4_Rules extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('api/rule', 'rule_id');
    }

    public function saveRel(Mage_Api_Model_Rules $rule) {
        $this->_getWriteAdapter()->beginTransaction();

        try {
            $roleId = $rule->getRoleId();
            $this->_getWriteAdapter()->delete($this->getMainTable(), "role_id = {$roleId}");
            $masterResources = Mage::getModel('api/roles')->getResourcesList2D();
            $masterAdmin = false;
            if ( $postedResources = $rule->getResources() ) {
                foreach ($masterResources as $index => $resName) {
                    if ( !$masterAdmin ) {
                        $permission = ( in_array($resName, $postedResources) )? 'allow' : 'deny';
                        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                            'role_type' 	=> 'G',
                            'resource_id' 	=> trim($resName, '/'),
                            'privileges' 	=> '', # FIXME !!!
                            'assert_id' 	=> 0,
                            'role_id' 		=> $roleId,
                            'permission'	=> $permission
                            ));
                    }
                    if ( $resName == 'all' && $permission == 'allow' ) {
                        $masterAdmin = true;
                    }
                }
            }

            $this->_getWriteAdapter()->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_getWriteAdapter()->rollBack();
        }
    }
}
