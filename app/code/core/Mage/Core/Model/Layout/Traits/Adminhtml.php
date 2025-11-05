<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Validator for custom layout update
 *
 * Validator checked XML validation and protected expressions
 *
 * @package    Mage_Core
 */
trait Mage_Core_Model_Layout_Traits_Adminhtml
{
    public function getBlockAdminhtmlBreadcrumbs(): ?Mage_Adminhtml_Block_Widget_Breadcrumbs
    {
        $block = $this->getBlockByName('breadcrumbs');
        if (!$block instanceof Mage_Adminhtml_Block_Widget_Breadcrumbs) {
            return null;
        }

        return $block;
    }

    public function getBlockAdminhtmlHead(): ?Mage_Adminhtml_Block_Page_Head
    {
        $block = $this->getBlockByName('head');
        if (!$block instanceof Mage_Adminhtml_Block_Page_Head) {
            return null;
        }

        return $block;
    }

    public function getBlockAdminhtmlMenu(): ?Mage_Adminhtml_Block_Page_Menu
    {
        $block = $this->getBlockByName('menu');
        if (!$block instanceof Mage_Adminhtml_Block_Page_Menu) {
            return null;
        }

        return $block;
    }
}
