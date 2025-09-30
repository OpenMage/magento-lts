<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * users block
 *
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
