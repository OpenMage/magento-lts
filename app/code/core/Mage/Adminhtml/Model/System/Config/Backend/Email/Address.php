<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * System config email field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Email_Address extends Mage_Core_Model_Config_Data
{
    /**
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $email = $this->getValue();
        $validator  = Validation::createValidator();
        if ($validator->validate($email, new Assert\Email())->count() > 0) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid email address "%s".', $email));
        }
        return $this;
    }
}
