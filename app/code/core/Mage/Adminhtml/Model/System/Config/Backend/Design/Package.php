<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Design_Package extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (empty($value)) {
            throw new Exception('package name is empty.');
        }
        if (!Mage::getDesign()->designPackageExists($value)) {
            throw new Exception('package with this name does not exist and cannot be set.');
        }
        return $this;
    }
}
