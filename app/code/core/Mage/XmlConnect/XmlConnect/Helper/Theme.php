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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Theme extends Mage_Adminhtml_Helper_Data
{
    /**
     * Color Themes Cache
     *
     * @param array|null
     */
    protected $_themeArray = null;

    /**
     * Return for Color Themes Fields array.
     *
     * @return array
     */
    public function getThemeAjaxParameters()
    {
        $themesArray = array (
            'conf_native_navigationBar_tintColor' => 'conf[native][navigationBar][tintColor]',
            'conf_native_body_primaryColor' => 'conf[native][body][primaryColor]',
            'conf_native_body_secondaryColor' => 'conf[native][body][secondaryColor]',
            'conf_native_categoryItem_backgroundColor' => 'conf[native][categoryItem][backgroundColor]',
            'conf_native_categoryItem_tintColor' => 'conf[native][categoryItem][tintColor]',

            'conf_extra_fontColors_header' => 'conf[extra][fontColors][header]',
            'conf_extra_fontColors_primary' => 'conf[extra][fontColors][primary]',
            'conf_extra_fontColors_secondary' => 'conf[extra][fontColors][secondary]',
            'conf_extra_fontColors_price' => 'conf[extra][fontColors][price]',

            'conf_native_body_backgroundColor' => 'conf[native][body][backgroundColor]',
            'conf_native_body_scrollBackgroundColor' => 'conf[native][body][scrollBackgroundColor]',
            'conf_native_itemActions_relatedProductBackgroundColor' => 'conf[native][itemActions][relatedProductBackgroundColor]'
        );
        return $themesArray;
    }

    /**
     * Returns JSON ready Themes array
     *
     * @param bool $flushCache -    load defaults
     * @return array
     */
    public function getAllThemesArray($flushCache = false)
    {
        $result = array();
        $themes = $this->getAllThemes($flushCache);
        foreach ($themes as $theme) {
            $result[$theme->getName()] = $theme->getFormData();
        }
        return $result;
    }

    /**
     *  Reads directory media/xmlconnect/themes/*
     *
     * @param bool $flushCache  - Reads default color Themes
     * @return array - (of Mage_XmlConnect_Model_Theme)
     */
    public function getAllThemes($flushCache = false)
    {
        if (!$this->_themeArray || $flushCache) {
            $saveLibxmlErrors = libxml_use_internal_errors(TRUE);
            $this->_themeArray = array();
            $themeDir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'themes';
            $io = new Varien_Io_File();
            $io->open(array('path' => $themeDir));

            $fileList = $io->ls(Varien_Io_File::GREP_FILES);
            foreach ($fileList as $file) {
                $src = $themeDir . DS . $file['text'];
                if (is_readable($src)) {
                    try {
                        $theme = Mage::getModel('xmlconnect/theme', $src);
                        $this->_themeArray[$theme->getName()] = $theme;
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
            libxml_use_internal_errors($saveLibxmlErrors);
        }
        return $this->_themeArray;
    }

    /**
     * Reset all theme color changes
     * Copy media/xmlconnect/themes/default/* to media/xmlconnect/themes/*
     *
     * @return void
     */
    public function resetAllThemes()
    {
        $themeDir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'themes';
        $defaultThemeDir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'themes' . DS . 'default';

        $io = new Varien_Io_File();
        $io->open(array('path'=>$defaultThemeDir));
        $fileList = $io->ls(Varien_Io_File::GREP_FILES);
        foreach ($fileList as $file) {
            $f = $file['text'];
            $src = $defaultThemeDir . DS . $f;
            $dst = $themeDir . DS .$f;
            if ($io->isWriteable($dst)) {
                try {
                    if (!$io->cp($src, $dst)) {
                        Mage::throwException(Mage::helper('xmlconnect')->__('Can\t copy file "%s" to "%s".', $src, $dst));
                    } else {
                        $io->chmod($dst, 0755);
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

    /**
     * Get theme object by name
     *
     * @param string $name
     * @return Mage_XmlConnect_Model_Theme|null
     */
    public function getThemeByName($name)
    {
        $themes = $this->getAllThemes();
        $theme = isset($themes[$name]) ? $themes[$name] : null;
        return $theme;
    }

    /**
     * Return predefined custom theme name
     *
     * @return string
     */
    public function getCustomThemeName()
    {
        return 'custom';
    }

    /**
     * Return predefined default theme name
     *
     * @return string
     */
    public function getDefaultThemeName()
    {
        return 'default';
    }
}
