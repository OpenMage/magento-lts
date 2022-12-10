<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * EAV Entity Attribute Image File Data Model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Attribute_Data_Image extends Mage_Eav_Model_Attribute_Data_File
{
    /**
     * Validate file by attribute validate rules
     * Return array of errors
     *
     * @param array $value
     * @return array
     */
    protected function _validateByRules($value)
    {
        $label  = Mage::helper('eav')->__($this->getAttribute()->getStoreLabel());
        $rules  = $this->getAttribute()->getValidateRules();

        $imageProp = @getimagesize($value['tmp_name']);

        if (!is_uploaded_file($value['tmp_name']) || !$imageProp) {
            return [
                Mage::helper('eav')->__('"%s" is not a valid file', $label)
            ];
        }

        $allowImageTypes = [
            1   => 'gif',
            2   => 'jpg',
            3   => 'png',
        ];

        if (!isset($allowImageTypes[$imageProp[2]])) {
            return [
                Mage::helper('eav')->__('"%s" is not a valid image format', $label)
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
