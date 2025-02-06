<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_ListMode
{
    public function toOptionArray()
    {
        return [
            //array('value'=>'', 'label'=>''),
            ['value' => 'grid', 'label' => Mage::helper('adminhtml')->__('Grid Only')],
            ['value' => 'list', 'label' => Mage::helper('adminhtml')->__('List Only')],
            ['value' => 'grid-list', 'label' => Mage::helper('adminhtml')->__('Grid (default) / List')],
            ['value' => 'list-grid', 'label' => Mage::helper('adminhtml')->__('List (default) / Grid')],
        ];
    }
}
