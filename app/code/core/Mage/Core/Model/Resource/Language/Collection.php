<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Core Language Resource collection
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Language_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core/language');
    }

    /**
     *  Convert collection items to array of select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('language_code', 'language_title', ['title' => 'language_title']);
    }

    /**
     * Convert items array to hash for select options
     *
     * @return  array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('language_code', 'language_title');
    }
}
