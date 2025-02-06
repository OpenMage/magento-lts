<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Install
 */

/**
 * Installation event observer
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Model_Observer
{
    public function bindLocale($observer)
    {
        if ($locale = $observer->getEvent()->getLocale()) {
            if ($choosedLocale = Mage::getSingleton('install/session')->getLocale()) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }

    public function installFailure($observer)
    {
        echo '<h2>There was a problem proceeding with Magento installation.</h2>';
        echo '<p>Please contact developers with error messages on this page.</p>';
        echo Mage::printException($observer->getEvent()->getException());
    }
}
