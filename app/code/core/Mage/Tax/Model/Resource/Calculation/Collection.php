<?php
/**
 * Tax Calculation Collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Tax
 * @method Mage_Tax_Model_Calculation[] getItems()
 */
class Mage_Tax_Model_Resource_Calculation_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/calculation');
    }
}
