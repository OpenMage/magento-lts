<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rating
 */

/**
 * Ratings entity model
 *
 * @category   Mage
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating_Entity _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Entity getResource()
 * @method string getEntityCode()
 * @method $this setEntityCode(string $value)
 */
class Mage_Rating_Model_Rating_Entity extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rating/rating_entity');
    }

    /**
     * @param string $entityCode
     * @return int
     */
    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
