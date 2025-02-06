<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Language resource model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Language extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/language', 'language_code');
    }
}
