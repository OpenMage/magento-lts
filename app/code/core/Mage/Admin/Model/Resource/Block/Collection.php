<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * Admin permissions block collection
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Admin_Model_Resource_Block_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('admin/block');
    }
}
