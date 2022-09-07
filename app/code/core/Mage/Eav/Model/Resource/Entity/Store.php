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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Entity store resource model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Store extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/entity_store', 'entity_store_id');
    }

    /**
     * Load an object by entity type and store
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $entityTypeId
     * @param int $storeId
     * @return bool
     */
    public function loadByEntityStore(Mage_Core_Model_Abstract $object, $entityTypeId, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $bind    = [
            ':entity_type_id' => $entityTypeId,
            ':store_id'       => $storeId
        ];
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->forUpdate(true)
            ->where('entity_type_id = :entity_type_id')
            ->where('store_id = :store_id');
        $data = $adapter->fetchRow($select, $bind);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }
}
