<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ImportExport config model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Config
{
    /**
     * Get data about models from specified config key.
     *
     * @static
     * @param string $configKey
     * @throws Mage_Core_Exception
     * @return array
     */
    public static function getModels($configKey)
    {
        $entities = [];

        foreach (Mage::getConfig()->getNode($configKey)->asCanonicalArray() as $entityType => $entityParams) {
            if (empty($entityParams['model_token'])) {
                Mage::throwException(Mage::helper('importexport')->__('Node does not has model token tag'));
            }
            $entities[$entityType] = [
                'model' => $entityParams['model_token'],
                'label' => empty($entityParams['label']) ? $entityType : $entityParams['label'],
            ];
        }
        return $entities;
    }

    /**
     * Get model params as combo-box options.
     *
     * @static
     * @param string $configKey
     * @param bool $withEmpty OPTIONAL Include 'Please Select' option or not
     * @return array
     */
    public static function getModelsComboOptions($configKey, $withEmpty = false)
    {
        $options = [];

        if ($withEmpty) {
            $options[] = ['label' => Mage::helper('importexport')->__('-- Please Select --'), 'value' => ''];
        }
        foreach (self::getModels($configKey) as $type => $params) {
            $options[] = ['value' => $type, 'label' => $params['label']];
        }
        return $options;
    }

    /**
     * Get model params as array of options.
     *
     * @static
     * @param string $configKey
     * @param bool $withEmpty OPTIONAL Include 'Please Select' option or not
     * @return array
     */
    public static function getModelsArrayOptions($configKey, $withEmpty = false)
    {
        $options = [];
        if ($withEmpty) {
            $options[0] = Mage::helper('importexport')->__('-- Please Select --');
        }
        foreach (self::getModels($configKey) as $type => $params) {
            $options[$type] = $params['label'];
        }
        return $options;
    }
}
