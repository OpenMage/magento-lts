<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Directory
 */
class Mage_Directory_Block_Adminhtml_Frontend_Region_Updater extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);

        return $html . ("<script type=\"text/javascript\">var updater = new RegionUpdater('tax_defaults_country', 'tax_region', 'tax_defaults_region', " . Mage::helper('directory')->getRegionJsonByStore() . ", 'disable');</script>");
    }
}
