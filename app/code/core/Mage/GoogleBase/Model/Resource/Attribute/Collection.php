<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * GoogleBase Attributes collection
 *
 * @deprecated after 1.5.1.0
 * @category    Mage
 * @package     Mage_GoogleBase
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Resource_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Whether to join attribute_set_id to attributes or not
     *
     * @var boolean
     */
    protected $_joinAttributeSetFlag     = true;

    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('googlebase/attribute');
    }

    /**
     * Filter collection by attribute set id
     *
     * @param int $attributeSetId
     * @param string $targetCountry
     * @return Mage_GoogleBase_Model_Resource_Attribute_Collection
     */
    public function addAttributeSetFilter($attributeSetId, $targetCountry)
    {
        if (!$this->getJoinAttributeSetFlag()) {
            return $this;
        }
        $select = $this->getSelect();
        $select->where('attribute_set_id = ?', $attributeSetId);
        $select->where('target_country = ?', $targetCountry);
        return $this;
    }

    /**
     * Add type filter
     *
     * @param int $type_id
     * @return Mage_GoogleBase_Model_Resource_Attribute_Collection
     */
    public function addTypeFilter($type_id)
    {
        $this->getSelect()->where('main_table.type_id = ?', $type_id);
        return $this;
    }

    /**
     * Load data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_GoogleBase_Model_Resource_Attribute_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        if ($this->getJoinAttributeSetFlag()) {
            $this->_joinAttributeSet();
        }
        parent::load($printQuery, $logQuery);
        return $this;
    }

    /**
     * Join attribute set
     *
     * @return Mage_GoogleBase_Model_Resource_Attribute_Collection
     */
    protected function _joinAttributeSet()
    {
        $this->getSelect()
            ->joinInner(
                array('types'=>$this->getTable('googlebase/types')),
                'main_table.type_id=types.type_id',
                array('attribute_set_id' => 'types.attribute_set_id',
                    'target_country' => 'types.target_country')
            );
        return $this;
    }

    /**
     * retrieve Flag
     *
     * @return boolean
     */
    public function getJoinAttributeSetFlag()
    {
        return $this->_joinAttributeSetFlag;
    }

    /**
     * Set flag
     *
     * @param unknown_type $flag
     * @return boolean
     */
    public function setJoinAttributeSetFlag($flag)
    {
        return $this->_joinAttributeSetFlag = (bool)$flag;
    }
}
