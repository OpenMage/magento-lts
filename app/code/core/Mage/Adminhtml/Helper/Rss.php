<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Default rss helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Rss extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Adminhtml';

    /**
     * @param string $path
     * @return void
     * @see Mage_Rss_Helper_Data::authAdmin()
     */
    public function authAdmin($path)
    {
        $session = Mage::getSingleton('rss/session');
        if ($session->isAdminLoggedIn()) {
            return;
        }
        list($username, $password) = Mage::helper('core/http')->authValidate();
        $adminSession = Mage::getModel('admin/session');
        $user = $adminSession->login($username, $password);
        if ($user && $user->getId() && $user->getIsActive() == '1' && $adminSession->isAllowed($path)) {
            $session->setAdmin($user);
        } else {
            Mage::helper('core/http')->authFailed();
        }
    }
}
