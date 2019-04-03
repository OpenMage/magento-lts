<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
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
    static public function getUserTypes($asOptionArray = false)
    {
        $userTypes = array();

        /** @var $helper Mage_Api2_Helper_Data */
        $helper = Mage::helper('api2');

        foreach ($helper->getUserTypes() as $modelPath) {
            /** @var $userModel Mage_Api2_Model_Auth_User_Abstract */
            $userModel = Mage::getModel($modelPath);

            if ($asOptionArray) {
                $userTypes[] = array('value' => $userModel->getType(), 'label' => $userModel->getLabel());
            } else {
                $userTypes[$userModel->getType()] = $userModel->getLabel();
            }
        }
        return $userTypes;
    }
}
