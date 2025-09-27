<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Country_Full extends Mage_Adminhtml_Model_System_Config_Source_Country
{
    public function toOptionArray($isMultiselect = false)
    {
        return parent::toOptionArray(true);
    }
}
