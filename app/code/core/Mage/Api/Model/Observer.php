<?php

class Mage_Api_Model_Observer
{
    /**
     * Upgrade the hash version, if needed
     *
     * @param Varien_Event_Observer $observer
     */
    public function apiAuthenticated($observer)
    {
        $encryptor = Mage::helper('core')->getEncryptor();

        /* @var $user Mage_Api_Model_User */
        $user = $observer->getModel();
        if ($encryptor->passwordHashNeedsUpgrade($user->getApiKey())) {
            $user->setApiKey($observer->getApiKey())->save();
        }
    }
}
