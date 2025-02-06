<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
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
