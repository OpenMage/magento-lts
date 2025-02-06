<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Flag model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Flag extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/flag', 'flag_id');
    }
}
