<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * user roles block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Roles extends Mage_Adminhtml_Block_Template
{
    /**
     * Get URL of adding new record
     *
     * @return string
     */
    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/editrole');
    }

    /**
     * Get URL for refreshing role-rule relations
     *
     * @return string
     */
    public function getRefreshRolesUrl()
    {
        return $this->getUrl('*/*/refreshroles');
    }

    /**
     * Get grid HTML
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChild('grid')->toHtml();
    }
}
