<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
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
