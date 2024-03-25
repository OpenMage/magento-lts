<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Catalog Grid Config Advanced Helper Contract
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
interface Mage_Adminhtml_Helper_Widget_Grid_Config_Interface
{
    /**
     * Collection object
     * @param Varien_Data_Collection_Db $collection
     *
     * return $this
     */
    public function applyAdvancedGridCollection(Varien_Data_Collection_Db $collection);

    /**
     * Adminhtml grid widget block
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     *
     * return $this
     */
    public function applyAdvancedGridColumn(Mage_Adminhtml_Block_Widget_Grid $gridBlock);
}
