<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Package
{
    public function toOptionArray()
    {
        $dir = new Varien_Io_File();
        #$dir->open(array('path'=>Mage::getStore))
    }
}
