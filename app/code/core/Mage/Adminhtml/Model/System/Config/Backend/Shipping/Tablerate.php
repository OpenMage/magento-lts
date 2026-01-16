<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Backend model for shipping table rates CSV importing
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Shipping_Tablerate extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::getResourceModel('shipping/carrier_tablerate')->uploadAndImport($this);
        return $this;
    }
}
