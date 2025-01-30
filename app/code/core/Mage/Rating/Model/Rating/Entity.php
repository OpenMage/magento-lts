<?php

/**
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
