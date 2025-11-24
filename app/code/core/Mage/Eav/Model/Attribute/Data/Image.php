<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Image File Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Image extends Mage_Eav_Model_Attribute_Data_File
{
    /**
     * Validate file by attribute validate rules
     * Return array of errors
     *
     * @param array $value
     * @return array
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _validateByRules($value)
    {
        $label  = Mage::helper('eav')->__($this->getAttribute()->getStoreLabel());
        $rules  = $this->getAttribute()->getValidateRules();

        $imageProp = @getimagesize($value['tmp_name']);

        if (!is_uploaded_file($value['tmp_name']) || !$imageProp) {
            return [
                Mage::helper('eav')->__('"%s" is not a valid file', $label),
            ];
        }

        $allowImageTypes = [
            1   => 'gif',
            2   => 'jpg',
            3   => 'png',
            18  => 'webp',
        ];

        if (!isset($allowImageTypes[$imageProp[2]])) {
            return [
                Mage::helper('eav')->__('"%s" is not a valid image format', $label),
            ];
        }

        // modify image name
        $extension  = pathinfo($value['name'], PATHINFO_EXTENSION);
        if ($extension != $allowImageTypes[$imageProp[2]]) {
            $value['name'] = pathinfo($value['name'], PATHINFO_FILENAME) . '.' . $allowImageTypes[$imageProp[2]];
        }

        $errors = [];
        if (!empty($rules['max_file_size'])) {
            $size = $value['size'];
            if ($rules['max_file_size'] < $size) {
                $errors[] = Mage::helper('eav')->__('"%s" exceeds the allowed file size.', $label);
            }
        }

        if (!empty($rules['max_image_width'])) {
            if ($rules['max_image_width'] < $imageProp[0]) {
                $r = $rules['max_image_width'];
                $errors[] = Mage::helper('eav')->__('"%s" width exceeds allowed value of %s px.', $label, $r);
            }
        }

        if (!empty($rules['max_image_heght'])) {
            if ($rules['max_image_heght'] < $imageProp[1]) {
                $r = $rules['max_image_heght'];
                $errors[] = Mage::helper('eav')->__('"%s" height exceeds allowed value of %s px.', $label, $r);
            }
        }

        return $errors;
    }
}
