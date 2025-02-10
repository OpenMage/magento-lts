<?php
/**
 * Eav Resource Entity Type Collection Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }
}
