<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Administrator account install block
 *
 * @package    Mage_Install
 */
class Mage_Install_Block_Admin extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/create_admin.phtml');
    }

    public function getPostUrl()
    {
        return $this->getUrl('*/*/administratorPost');
    }

    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object(Mage::getSingleton('install/session')->getAdminData(true));
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve minimum length of admin password
     *
     * @return int
     */
    public function getMinAdminPasswordLength()
    {
        return Mage::getModel('admin/user')->getMinAdminPasswordLength();
    }
}
