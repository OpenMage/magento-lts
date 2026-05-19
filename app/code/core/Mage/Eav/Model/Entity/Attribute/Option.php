<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Emtity attribute option model
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option            _getResource()
 * @method int                                                        getAttributeId()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection getCollection()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option            getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection getResourceCollection()
 * @method int                                                        getSortOrder()
 * @method $this                                                      setAttributeId(int $value)
 * @method $this                                                      setSortOrder(int $value)
 */
class Mage_Eav_Model_Entity_Attribute_Option extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_attribute_option');
    }

    /**
     * Retrieve swatch hex value
     *
     * @return false|string
     */
    public function getSwatchValue()
    {
        $swatch = Mage::getModel('eav/entity_attribute_option_swatch')
            ->load($this->getId(), 'option_id');
        if (!$swatch->getId()) {
            return false;
        }

        return $swatch->getValue();
    }
}
