<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default config helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Config extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Adminhtml';

    /**
     * Return information array of input types
     */
    public function getInputTypes(?string $inputType = null): array
    {
        $inputTypes = [
            'color' => [
                'backend_model' => 'adminhtml/system_config_backend_color',
            ],
        ];

        if (is_null($inputType)) {
            return $inputTypes;
        } elseif (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return [];
    }

    /**
     * Return default backend model by input type
     */
    public function getBackendModelByInputType(string $inputType): ?string
    {
        $inputTypes = $this->getInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * Get field backend model by field config node
     */
    public function getBackendModelByFieldConfig(Varien_Simplexml_Element $fieldConfig): ?string
    {
        if (isset($fieldConfig->backend_model)) {
            return (string) $fieldConfig->backend_model;
        }
        if (isset($fieldConfig->frontend_type)) {
            return $this->getBackendModelByInputType((string) $fieldConfig->frontend_type);
        }
        return null;
    }
}
