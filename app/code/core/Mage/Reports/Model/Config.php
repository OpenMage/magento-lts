<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration for reports
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Config extends Varien_Object
{
    /**
      * @return string
      */
    public function getGlobalConfig()
    {
        $dom = new DOMDocument();
        $dom -> load(Mage::getModuleDir('etc', 'Mage_Reports').DS.'flexConfig.xml');

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
        return file_get_contents(Mage::getModuleDir('etc', 'Mage_Reports').DS.'flexLanguage.xml');
    }

    /**
      * @return false|string
      */
    public function getDashboard()
    {
        return file_get_contents(Mage::getModuleDir('etc', 'Mage_Reports').DS.'flexDashboard.xml');
    }
}
