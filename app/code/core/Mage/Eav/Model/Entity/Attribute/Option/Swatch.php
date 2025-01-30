<?php

/**
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Entity attribute swatch model
 *
 * @category   Mage
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
