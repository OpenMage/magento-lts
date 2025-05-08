<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Class Mage_Admin_Model_Variable
 *
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
