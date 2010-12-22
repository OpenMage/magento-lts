<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default helper of the module
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Retrieve file system path for local extension packages
     * Return path with last directory separator
     *
     * @return string
     */
    public function getLocalPackagesPath()
    {
        return Mage::getBaseDir('var') . DS . 'connect' . DS;
    }

    /**
     * Load local package data array
     *
     * @param string $packageName without extension
     * @return array|false
     */
    public function loadLocalPackage($packageName)
    {
        $path = $this->getLocalPackagesPath();
        $xmlFile = $path . $packageName . '.xml';
        $serFile = $path . $packageName . '.ser';

        if (file_exists($xmlFile) && is_readable($xmlFile)) {
            $xml  = simplexml_load_file($xmlFile);
            $data = Mage::helper('core')->xmlToAssoc($xml);
            if (!empty($data)) {
                return $data;
            }
        }

        if (file_exists($serFile) && is_readable($xmlFile)) {
            $data = unserialize(file_get_contents($serFile));
            if (!empty($data)) {
                return $data;
            }
        }

        return false;
    }
}
