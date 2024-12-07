<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating entity resource
 *
 * @category   Mage
 * @package    Mage_Rating
 */
class Mage_Rating_Model_Resource_Rating_Entity extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Rating entity resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('rating/rating_entity', 'entity_id');
    }

    /**
     * Return entity_id by entityCode
     *
     * @param string $entityCode
     * @return int
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
