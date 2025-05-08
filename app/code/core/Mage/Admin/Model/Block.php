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
 * Class Mage_Admin_Model_Block
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Admin_Model_Resource_Block _getResource()
 * @method Mage_Admin_Model_Resource_Block getResource()
 * @method Mage_Admin_Model_Resource_Block_Collection getCollection()
 *
 * @method string getBlockName()
 * @method string getIsAllowed()
 */
class Mage_Admin_Model_Block extends Mage_Core_Model_Abstract
{
    public const BLOCK_NAME_REGEX = '/^[-_a-zA-Z0-9]+\/[-_a-zA-Z0-9\/]+$/';

    /**
     * Initialize variable model
     */
    protected function _construct()
    {
        $this->_init('admin/block');
    }

    /**
     * @return array|true
     * @throws Exception
     */
    public function validate()
    {
        $validator  = Validation::createValidator();
        $violations = [];
        $errors     = new ArrayObject();

        $violations[] = $validator->validate($this->getBlockName(), [
            new Assert\NotBlank([
                'message' => Mage::helper('adminhtml')->__('Block Name is required field.'),
            ]),
            new Assert\Regex([
                'pattern' => self::BLOCK_NAME_REGEX,
                'message' => Mage::helper('adminhtml')->__('Block Name is incorrect.'),
            ]),
            new Assert\Choice([
                'choices' => Mage::helper('admin/block')->getDisallowedBlockNames(),
                'match' => false,
                'message' => Mage::helper('adminhtml')->__('Block Name is disallowed.'),
            ]),
        ]);

        $violations[] = $validator->validate($this->getIsAllowed(), [
            new Assert\Choice([
                'choices' => ['0', '1'],
                'message' => Mage::helper('adminhtml')->__('Is Allowed is required field.'),
            ]),
        ]);

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
     * Check is block with such type allowed for parsing via blockDirective method
     *
     * @param string $type
     * @return bool
     */
    public function isTypeAllowed($type)
    {
        return Mage::helper('admin/block')->isTypeAllowed($type);
    }
}
