<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Directory
 */

/**
 * Directory country format resource model
 *
 * @category   Mage
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Resource_Country_Format extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country_format', 'country_format_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [[
            'field' => ['country_id', 'type'],
            'title' => Mage::helper('directory')->__('Country and Format Type combination should be unique'),
        ]];
        return $this;
    }
}
