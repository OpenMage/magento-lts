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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attributes grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
abstract class Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('attributeGrid');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare default grid column
     *
     * @return Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('frontend_label', [
            'header' => Mage::helper('eav')->__('Attribute Label'),
            'index' => 'frontend_label',
        ]);

        $this->addColumn('attribute_code', [
            'header' => Mage::helper('eav')->__('Attribute Code'),
            'index' => 'attribute_code',
        ]);

        $this->addColumn('is_required', [
            'header' => Mage::helper('eav')->__('Required'),
            'index' => 'is_required',
            'type' => 'options',
            'options' => [
                '1' => Mage::helper('eav')->__('Yes'),
                '0' => Mage::helper('eav')->__('No'),
            ],
            'align' => 'center',
        ]);

        $this->addColumn('is_user_defined', [
            'header' => Mage::helper('eav')->__('System'),
            'index' => 'is_user_defined',
            'type' => 'options',
            'align' => 'center',
            'options' => [
                '0' => Mage::helper('eav')->__('Yes'),   // intended reverted use
                '1' => Mage::helper('eav')->__('No'),    // intended reverted use
            ],
        ]);

        return $this;
    }

    /**
     * Return url of given row
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['attribute_id' => $row->getAttributeId()]);
    }
}
