<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Eav_Block_Adminhtml_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if ($entity_type = Mage::registry('entity_type')) {
            /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection */
            $collection = Mage::getResourceModel($entity_type->getEntityAttributeCollection());
            $collection->setEntityTypeFilter($entity_type->getEntityTypeId());
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare attributes grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_global', [
            'header' => Mage::helper('eav')->__('Scope'),
            'sortable' => true,
            'index' => 'is_global',
            'type' => 'options',
            'options' => array(
                Mage_Eav_Model_Entity_Attribute::SCOPE_STORE => Mage::helper('eav')->__('Store View'),
                Mage_Eav_Model_Entity_Attribute::SCOPE_WEBSITE => Mage::helper('eav')->__('Website'),
                Mage_Eav_Model_Entity_Attribute::SCOPE_GLOBAL => Mage::helper('eav')->__('Global'),
            ),
            'align' => 'center',
        ]);

        return $this;
    }
}
