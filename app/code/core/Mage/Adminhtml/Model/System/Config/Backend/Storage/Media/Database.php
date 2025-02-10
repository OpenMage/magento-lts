<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Storage_Media_Database extends Mage_Core_Model_Config_Data
{
    /**
     * Create db structure
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $helper = Mage::helper('core/file_storage');
        $helper->getStorageModel(null, ['init' => true]);

        return $this;
    }
}
