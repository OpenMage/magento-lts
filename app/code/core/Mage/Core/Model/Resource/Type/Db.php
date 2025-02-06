<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Resource_Type_Db extends Mage_Core_Model_Resource_Type_Abstract
{
    public function __construct()
    {
        $this->_entityClass = 'Mage_Core_Model_Resource_Entity_Table';
    }
}
