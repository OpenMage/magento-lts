<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage Core convert model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Convert extends Mage_Dataflow_Model_Convert_Profile_Collection
{
    public function __construct()
    {
        $classArr = explode('_', get_class($this));
        $moduleName = $classArr[0] . '_' . $classArr[1];
        $etcDir = Mage::getConfig()->getModuleDir('etc', $moduleName);

        $fileName = $etcDir . DS . 'convert.xml';
        if (is_readable($fileName)) {
            $data = file_get_contents($fileName);
            $this->importXml($data);
        }
    }

    /**
     * @param string $type
     * @return mixed|string
     */
    public function getClassNameByType($type)
    {
        if (str_contains($type, '/')) {
            return Mage::getConfig()->getModelClassName($type);
        }
        return parent::getClassNameByType($type);
    }
}
