<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * users block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Api_Users extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('api/users.phtml');
    }

    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/edituser');
    }

    public function getGridHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/api_grid_user')->toHtml();
    }
}
