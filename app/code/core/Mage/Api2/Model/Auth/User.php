<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API auth user
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Auth_User
{
    /**
     * Get options in "key-value" format
     *
     * @param bool $asOptionArray OPTIONAL If TRUE - return an options array, plain array - otherwise
     * @return array
     */
    public static function getUserTypes($asOptionArray = false)
    {
        $userTypes = [];

        /** @var Mage_Api2_Helper_Data $helper */
        $helper = Mage::helper('api2');

        foreach ($helper->getUserTypes() as $modelPath) {
            /** @var Mage_Api2_Model_Auth_User_Abstract $userModel */
            $userModel = Mage::getModel($modelPath);

            if ($asOptionArray) {
                $userTypes[] = ['value' => $userModel->getType(), 'label' => $userModel->getLabel()];
            } else {
                $userTypes[$userModel->getType()] = $userModel->getLabel();
            }
        }

        return $userTypes;
    }
}
