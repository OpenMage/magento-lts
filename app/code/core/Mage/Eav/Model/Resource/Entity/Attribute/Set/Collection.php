<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Resource Attribute Set Collection
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_attribute_set');
    }

    /**
     * Add filter by entity type id to collection
     *
     * @param int $typeId
     * @return $this
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this->addFieldToFilter('entity_type_id', $typeId);
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('attribute_set_id', 'attribute_set_name');
    }

    /**
     * Convert collection items to select options hash array
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('attribute_set_id', 'attribute_set_name');
    }
}
