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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav Form Element Resource Collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Mysql4_Form_Element_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('eav/form_element');
    }

    /**
     * Add Form Type filter to collection
     *
     * @param Mage_Eav_Model_Form_Type|int $type
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    public function addTypeFilter($type)
    {
        if ($type instanceof Mage_Eav_Model_Form_Type) {
            $type = $type->getId();
        }

        $this->addFieldToFilter('type_id', $type);

        return $this;
    }


    /**
     * Add Form Fieldset filter to collection
     *
     * @param Mage_Eav_Model_Form_Fieldset|int $fieldset
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    public function addFieldsetFilter($fieldset)
    {
        if ($fieldset instanceof Mage_Eav_Model_Form_Fieldset) {
            $fieldset = $fieldset->getId();
        }

        $this->addFieldToFilter('main_table.fieldset_id', $fieldset);

        return $this;
    }

    /**
     * Add Attribute filter to collection
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|int $attribute
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    public function addAttributeFilter($attribute)
    {
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $attribute = $attribute->getId();
        }

        $this->addFieldToFilter('main_table.attribute_id', $attribute);

        return $this;
    }

    /**
     * Set order by element sort order
     *
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    public function setSortOrder()
    {
        $this->setOrder('main_table.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Join attribute data
     *
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    protected function _joinAttributeData()
    {
        $this->getSelect()->join(
            array('eav_attribute' => $this->getTable('eav/attribute')),
            'main_table.attribute_id=eav_attribute.attribute_id',
            array('attribute_code', 'entity_type_id')
        );

        return $this;
    }

    /**
     * Load data (join attribute data)
     *
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->_joinAttributeData();
        }
        return parent::load($printQuery, $logQuery);
    }
}
