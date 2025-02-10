<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Seo_Product extends Mage_Core_Model_Config_Data
{
    /**
     * Refresh category url rewrites if configuration was changed
     *
     * @return $this
     */
    protected function _afterSave()
    {
        return $this;
    }
}
