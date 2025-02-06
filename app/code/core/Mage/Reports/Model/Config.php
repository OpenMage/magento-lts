<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Configuration for reports
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Config extends Varien_Object
{
    /**
      * @return string
      */
    public function getGlobalConfig()
    {
        $dom = new DOMDocument();
        $dom -> load(Mage::getModuleDir('etc', 'Mage_Reports') . DS . 'flexConfig.xml');

        $baseUrl = $dom -> createElement('baseUrl');
        $baseUrl -> nodeValue = Mage::getBaseUrl();

        $dom -> documentElement -> appendChild($baseUrl);

        return $dom -> saveXML();
    }

    /**
      * @return false|string
      */
    public function getLanguage()
    {
        return file_get_contents(Mage::getModuleDir('etc', 'Mage_Reports') . DS . 'flexLanguage.xml');
    }

    /**
      * @return false|string
      */
    public function getDashboard()
    {
        return file_get_contents(Mage::getModuleDir('etc', 'Mage_Reports') . DS . 'flexDashboard.xml');
    }
}
