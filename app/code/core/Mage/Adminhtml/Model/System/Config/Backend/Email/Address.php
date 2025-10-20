<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * System config email field backend model
 *
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
        if ($validator->validate($email, [new Assert\NotBlank(), new Assert\Email()])->count() > 0) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid email address "%s".', $email));
        }

        return $this;
    }
}
