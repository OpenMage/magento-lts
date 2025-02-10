<?php
/**
 * Entity attribute select source interface
Source is providing the selection options for user interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Eav
 */
interface Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions();

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value);
}
