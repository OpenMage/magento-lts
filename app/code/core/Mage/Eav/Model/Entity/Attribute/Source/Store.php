<?php
/**
 * Customer store_id attribute source
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Source_Store extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param bool $defaultValues   Argument has no effect, included for PHP 7.2 method signature compatibility
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if ($this->_options === null) {
            $this->_options = Mage::getResourceModel('core/store_collection')->load()->toOptionArray();
        }
        return $this->_options;
    }
}
