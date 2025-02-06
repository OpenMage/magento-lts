<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Denied extends Mage_Adminhtml_Block_Template
{
    public function hasAvailaleResources()
    {
        $user = Mage::getSingleton('admin/session')->getUser();
        if ($user && $user->hasAvailableResources()) {
            return true;
        }
        return false;
    }
}
