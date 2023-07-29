<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin rule resource model
 *
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Rules extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/rule', 'rule_id');
    }

    /**
     * Save ACL resources
     *
     * @param Mage_Admin_Model_Rules $rule
     */
    public function saveRel(Mage_Admin_Model_Rules $rule)
    {
        $adapter = $this->_getWriteAdapter();
        try {
            $adapter->beginTransaction();
            $roleId = $rule->getRoleId();

            $condition = [
                'role_id = ?' => (int) $roleId,
            ];

            $adapter->delete($this->getMainTable(), $condition);

            $postedResources = $rule->getResources();
            if ($postedResources) {
                $row = [
                    'role_type'   => 'G',
                    'resource_id' => 'all',
                    'privileges'  => '', // not used yet
                    'assert_id'   => 0,
                    'role_id'     => $roleId,
                    'permission'  => 'allow'
                ];

                // If all was selected save it only and nothing else.
                if ($postedResources === ['all']) {
                    $insertData = $this->_prepareDataForTable(new Varien_Object($row), $this->getMainTable());

                    $adapter->insert($this->getMainTable(), $insertData);
                } else {
                    foreach (Mage::getModel('admin/roles')->getResourcesList2D() as $index => $resName) {
                        $row['permission']  = (in_array($resName, $postedResources) ? 'allow' : 'deny');
                        $row['resource_id'] = trim($resName, '/');

                        $insertData = $this->_prepareDataForTable(new Varien_Object($row), $this->getMainTable());
                        $adapter->insert($this->getMainTable(), $insertData);
                    }
                }
            }

            $adapter->commit();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollBack();
            throw $e;
        } catch (Exception $e) {
            $adapter->rollBack();
            Mage::logException($e);
        }
    }
}
