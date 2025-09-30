<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Store _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Store getResource()
 * @method int getEntityTypeId()
 * @method $this setEntityTypeId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getIncrementPrefix()
 * @method $this setIncrementPrefix(string $value)
 * @method string getIncrementLastId()
 * @method $this setIncrementLastId(string $value)
 */
class Mage_Eav_Model_Entity_Store extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_store');
    }

    /**
     * Load entity by store
     *
     * @param int $entityTypeId
     * @param int $storeId
     * @return $this
     */
    public function loadByEntityStore($entityTypeId, $storeId)
    {
        $this->_getResource()->loadByEntityStore($this, $entityTypeId, $storeId);
        return $this;
    }
}
