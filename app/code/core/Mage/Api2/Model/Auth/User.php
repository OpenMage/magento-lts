<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API auth user
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User
{
    /**
     * Get options in "key-value" format
     *
     * @param boolean $asOptionArray OPTIONAL If TRUE - return an options array, plain array - otherwise
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
