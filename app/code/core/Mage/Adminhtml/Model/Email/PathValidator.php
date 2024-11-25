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

/**
 * Validator for Email Template
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Email_PathValidator extends Zend_Validate_Abstract
{
    /**
     * Returns true if and only if $value meets the validation requirements
     * If $value fails validation, then this method returns false
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $pathNode = is_array($value) ? array_shift($value) : $value;

        return $this->isEncryptedNodePath($pathNode);
    }

    /**
     * Return bool after checking the encrypted model in the path to config node
     *
     * @param string $path
     * @return bool
     */
    protected function isEncryptedNodePath($path)
    {
        /** @var Mage_Adminhtml_Model_Config $configModel */
        $configModel = Mage::getSingleton('adminhtml/config');

        return in_array((string) $path, $configModel->getEncryptedNodeEntriesPaths());
    }
}
