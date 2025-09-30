<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity attribute swatch model
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option_Swatch _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option_Swatch getResource()
 */
class Mage_Eav_Model_Entity_Attribute_Option_Swatch extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('eav/entity_attribute_option_swatch');
    }
}
