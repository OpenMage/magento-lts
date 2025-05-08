<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Class describing db table resource entity
 *
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
