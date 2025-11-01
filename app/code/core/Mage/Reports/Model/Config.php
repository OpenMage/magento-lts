<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Configuration for reports
 *
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
