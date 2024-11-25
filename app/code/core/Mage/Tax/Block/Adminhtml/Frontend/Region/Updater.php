<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Block_Adminhtml_Frontend_Region_Updater extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);

        $js = '<script type="text/javascript">
               var updater = new RegionUpdater("tax_defaults_country", "none", "tax_defaults_region", %s, "nullify");
               if(updater.lastCountryId) {
                   var tmpRegionId = $("tax_defaults_region").value;
                   var tmpCountryId = updater.lastCountryId;
                   updater.lastCountryId=false;
                   updater.update();
                   updater.lastCountryId = tmpCountryId;
                   $("tax_defaults_region").value = tmpRegionId;
               } else {
                   updater.update();
               }
               </script>';

        $html .= sprintf($js, Mage::helper('directory')->getRegionJsonByStore());
        return $html;
    }
}
