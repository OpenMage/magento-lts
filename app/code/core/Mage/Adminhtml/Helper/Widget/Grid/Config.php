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
 * Adminhtml Catalog Grid Config Advanced Helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Widget_Grid_Config extends Mage_Adminhtml_Helper_Widget_Grid_Config_Abstract
{
    /**
     * Collection object
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     *
     * return $this
     */
    public function applyAdvancedGridCollection($collection)
    {
        return $this;
    }

    /**
     * Adminhtml grid widget block
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     *
     * return $this
     */
    public function applyAdvancedGridColumn($gridBlock)
    {
        return $this;
    }
}
