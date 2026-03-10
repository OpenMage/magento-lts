<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Rules resource model
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Rules extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('api/rule', 'rule_id');
    }

    /**
     * Save rule
     */
    public function saveRel(Mage_Api_Model_Rules $rule)
    {
        $permission = '';
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $roleId = $rule->getRoleId();
            $adapter->delete($this->getMainTable(), ['role_id = ?' => $roleId]);
            $masterResources = Mage::getModel('api/roles')->getResourcesList2D();
            $masterAdmin = false;
            if ($postedResources = $rule->getResources()) {
                foreach ($masterResources as $resName) {
                    if (!$masterAdmin) {
                        $permission = (in_array($resName, $postedResources)) ? 'allow' : 'deny';
                        $adapter->insert($this->getMainTable(), [
                            'role_type'     => 'G',
                            'resource_id'   => trim($resName, '/'),
                            'api_privileges'    => null,
                            'assert_id'     => 0,
                            'role_id'       => $roleId,
                            'api_permission'    => $permission,
                        ]);
                    }

                    if ($resName == 'all' && $permission == 'allow') {
                        $masterAdmin = true;
                    }
                }
            }

            $adapter->commit();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollBack();
            throw $e;
        } catch (Exception) {
            $adapter->rollBack();
        }
    }
}
