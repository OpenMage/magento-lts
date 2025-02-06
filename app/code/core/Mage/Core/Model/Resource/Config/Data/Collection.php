<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Config data collection
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Config_Data_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core/config_data');
    }

    /**
     * Add scope filter to collection
     *
     * @param string $scope
     * @param int $scopeId
     * @param string $section
     * @return $this
     */
    public function addScopeFilter($scope, $scopeId, $section)
    {
        $this->addFieldToFilter('scope', $scope);
        $this->addFieldToFilter('scope_id', $scopeId);
        $this->addFieldToFilter('path', ['like' => $section . '/%']);
        return $this;
    }

    /**
     *  Add path filter
     *
     * @param string $section
     * @return $this
     */
    public function addPathFilter($section)
    {
        $this->addFieldToFilter('path', ['like' => $section . '/%']);
        return $this;
    }

    /**
     * Add value filter
     *
     * @param int|string $value
     * @return $this
     */
    public function addValueFilter($value)
    {
        $this->addFieldToFilter('value', ['like' => $value]);
        return $this;
    }
}
