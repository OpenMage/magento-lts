<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Class Mage_Admin_Model_Variable
 *
 * @category   Mage
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_Variable _getResource()
 * @method Mage_Admin_Model_Resource_Variable getResource()
 * @method Mage_Admin_Model_Resource_Variable_Collection getCollection()
 *
 * @method string getIsAllowed()
 * @method string getVariableName()
 */
class Mage_Admin_Model_Variable extends Mage_Core_Model_Abstract
{
    /**
     * Initialize variable model
     */
    protected function _construct()
    {
        $this->_init('admin/variable');
    }

    /**
     * @return array|true
     * @throws Exception
     */
    public function validate()
    {
        $validator  = Validation::createValidator();
        $violations = [];
        $errors = new ArrayObject();

        $violations[] = $validator->validate($this->getVariableName(), [
            new Assert\NotBlank([
                'message' => Mage::helper('adminhtml')->__('Variable Name is required field.'),
            ]),
            new Assert\Regex([
                'message' => Mage::helper('adminhtml')->__('Variable Name is incorrect.'),
                'pattern' => '/^[-_a-zA-Z0-9\/]*$/',
            ]),
        ]);

        $violations[] = $validator->validate($this->getIsAllowed(), [new Assert\Choice([
            'choices' => ['0', '1'],
            'message' => Mage::helper('adminhtml')->__('Is Allowed is required field.'),
        ])]);

        foreach ($violations as $violation) {
            foreach ($violation as $error) {
                $errors->append($error->getMessage());
            }
        }

        if (count($errors) === 0) {
            return true;
        }

        return (array) $errors;
    }

    /**
     * Check is config directive with given path can be parsed via configDirective method
     *
     * @param string $path
     * @return bool
     */
    public function isPathAllowed($path)
    {
        return Mage::helper('admin/variable')->isPathAllowed($path);
    }
}
