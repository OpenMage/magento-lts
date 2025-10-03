<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Ratings entity model
 *
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
     * @return string
     */
    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
