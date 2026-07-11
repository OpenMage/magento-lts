<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales report resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct() {}

    /**
     * Set main table and idField
     *
     * @param  string $table
     * @param  string $field
     * @return $this
     */
    public function init($table, $field = 'id')
    {
        $this->_init($table, $field);
        return $this;
    }
}
