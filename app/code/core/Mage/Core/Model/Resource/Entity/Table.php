<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
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
