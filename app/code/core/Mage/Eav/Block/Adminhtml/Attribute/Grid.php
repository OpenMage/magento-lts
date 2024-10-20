<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Adminhtml_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare grid collection object
     *
     * @return $this
     * @throws Exception
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
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_global', [
            'header' => Mage::helper('eav')->__('Scope'),
            'sortable' => true,
            'index' => 'is_global',
            'type' => 'options',
            'options' => [
                Mage_Eav_Model_Entity_Attribute::SCOPE_STORE => Mage::helper('eav')->__('Store View'),
                Mage_Eav_Model_Entity_Attribute::SCOPE_WEBSITE => Mage::helper('eav')->__('Website'),
                Mage_Eav_Model_Entity_Attribute::SCOPE_GLOBAL => Mage::helper('eav')->__('Global'),
            ],
            'align' => 'center',
        ]);

        return $this;
    }
}
