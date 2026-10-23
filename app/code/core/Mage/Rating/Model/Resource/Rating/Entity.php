<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Rating entity resource
 *
 * @package    Mage_Rating
 */
class Mage_Rating_Model_Resource_Rating_Entity extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('rating/rating_entity', 'entity_id');
    }

    /**
     * Return entity_id by entityCode
     *
     * @param  string $entityCode
     * @return string
     */
    public function getIdByCode($entityCode)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('rating_entity'), $this->getIdFieldName())
            ->where('entity_code = :entity_code');
        return $adapter->fetchOne($select, [':entity_code' => $entityCode]);
    }
}
