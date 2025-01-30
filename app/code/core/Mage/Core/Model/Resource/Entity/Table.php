<?php

/**
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class describing db table resource entity
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Entity_Table extends Mage_Core_Model_Resource_Entity_Abstract
{
    /**
     * @return String
     */
    public function getTable()
    {
        return $this->getConfig('table');
    }
}
