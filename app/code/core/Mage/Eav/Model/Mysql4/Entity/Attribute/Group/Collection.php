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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Init resource model for collection
     *
     */
    public function _construct()
    {
        $this->_init('eav/entity_attribute_group');
    }

    /**
     * Set Attribute Set Filter
     *
     * @param int $setId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection
     */
    public function setAttributeSetFilter($setId)
    {
        $this->getSelect()->where('main_table.attribute_set_id=?', $setId);
        $this->getSelect()->order('main_table.sort_order');
        return $this;
    }
}
