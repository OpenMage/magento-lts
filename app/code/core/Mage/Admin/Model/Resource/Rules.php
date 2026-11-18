<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin rule resource model
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Rules extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/rule', 'rule_id');
    }

    /**
     * Save ACL resources
     *
     * @throws Exception
     * @throws Mage_Core_Exception
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
                    'permission'  => 'allow',
                ];

                // If all was selected save it only and nothing else.
                if ($postedResources === ['all']) {
                    $insertData = $this->_prepareDataForTable(new Varien_Object($row), $this->getMainTable());

                    $adapter->insert($this->getMainTable(), $insertData);
                } else {
                    foreach (Mage::getModel('admin/roles')->getResourcesList2D() as $resName) {
                        $row['permission']  = (in_array($resName, $postedResources) ? 'allow' : 'deny');
                        $row['resource_id'] = trim($resName, '/');

                        $insertData = $this->_prepareDataForTable(new Varien_Object($row), $this->getMainTable());
                        $adapter->insert($this->getMainTable(), $insertData);
                    }
                }
            }

            // Commit the members' new ACL version with the rules: saving the role moved them to a new version
            // before its rules changed, and a session that rebuilt its ACL since then has the old rules.
            $reloaded = $this->_reloadRoleUsersAcl((int) $roleId);
            $adapter->commit();
        } catch (Mage_Core_Exception $mageCoreException) {
            $adapter->rollBack();
            throw $mageCoreException;
        } catch (Exception $exception) {
            $adapter->rollBack();
            Mage::logException($exception);
            return;
        }

        if ($reloaded) {
            $this->_getUserResource()->cleanMenuCache();
        }
    }

    /**
     * Make every member of the role rebuild its ACL on its next request
     *
     * @return bool                      whether the role has members
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _reloadRoleUsersAcl(int $roleId): bool
    {
        /** @var Mage_Admin_Model_Roles $role */
        $role = Mage::getModel('admin/roles')->setId($roleId);
        return $this->_getUserResource()->bumpAclVersion($role->getResource()->getRoleUsers($role)) > 0;
    }

    protected function _getUserResource(): Mage_Admin_Model_Resource_User
    {
        /** @var Mage_Admin_Model_Resource_User $resource */
        $resource = Mage::getResourceSingleton('admin/user');
        return $resource;
    }

    /**
     * Set resource ID as ID field name
     *
     * @return $this
     * @see Mage_Adminhtml_Block_Permissions_OrphanedResource_Grid::_prepareCollection()
     */
    public function setResourceIdAsIdFieldName()
    {
        $this->_idFieldName = 'resource_id';
        return $this;
    }

    /**
     * Delete orphaned resources
     *
     * @throws Mage_Core_Exception
     */
    public function deleteOrphanedResources(array $orphanedIds): int
    {
        if ($orphanedIds === []) {
            return 0;
        }

        $resourceIds = Mage::getModel('admin/roles')->getResourcesList2D();
        // Validate orphaned IDs are not in the list of valid resource IDs.
        $validIds = array_intersect($orphanedIds, $resourceIds);
        if ($validIds !== []) {
            throw new Mage_Core_Exception(
                Mage::helper('adminhtml')->__(
                    'The following role resource(s) are not orphaned: %s',
                    implode(', ', $validIds),
                ),
            );
        }

        return $this->_getWriteAdapter()
            ->delete($this->getMainTable(), ['resource_id IN (?)' => $orphanedIds]);
    }
}
