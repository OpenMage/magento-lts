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
 * Class Mage_Admin_Model_Block
 *
 * @category   Mage
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
    public const BLOCK_NAME_REGEX = '/[-_a-zA-Z0-9]+\/[-_a-zA-Z0-9\/]+$/';

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
            # @todo fix regex pattern
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
