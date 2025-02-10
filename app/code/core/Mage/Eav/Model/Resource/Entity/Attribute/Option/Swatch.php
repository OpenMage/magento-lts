<?php
/**
 * Entity attribute swatch resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Option_Swatch extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_option_swatch', 'option_id');
    }
}
