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
trait Mage_Core_Model_Layout_Traits_Frontend
{
    public function getBlockBreadcrumbs(): ?Mage_Page_Block_Html_Breadcrumbs
    {
        $block = $this->getBlockByName('breadcrumbs');
        if (!$block instanceof Mage_Page_Block_Html_Breadcrumbs) {
            return null;
        }

        return $block;
    }

    public function getBlockHead(): ?Mage_Page_Block_Html_Head
    {
        $block = $this->getBlockByName('head');
        if (!$block instanceof Mage_Page_Block_Html_Head) {
            return null;
        }

        return $block;
    }

    public function getBlockRoot(): ?Mage_Page_Block_Html
    {
        $block = $this->getBlockByName('root');
        if (!$block instanceof Mage_Page_Block_Html) {
            return null;
        }

        return $block;
    }
}
