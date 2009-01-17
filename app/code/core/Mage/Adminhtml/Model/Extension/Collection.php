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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Extension packages files collection
 *
 */
class Mage_Adminhtml_Model_Extension_Collection extends Mage_Adminhtml_Model_Extension_Collection_Abstract
{
    public static $allowDirs     = '/^[a-z0-9\.\-]+$/i';
    public static $allowFiles    = '/^[a-z0-9\.\-\_]+\.(xml|ser)$/i';
    public static $disallowFiles = '/^package\.xml$/i';

    /**
     * Get all packages identifiers
     *
     * @return array
     */
    protected function _fetchPackages()
    {
        $baseDir = Mage::getBaseDir('var') . DS . 'pear';
        $files = array();
        $this->_collectRecursive($baseDir,  $files);
        $result = array();
        foreach ($files as $file) {
            $file = preg_replace(array('/^' . preg_quote($baseDir . DS, '/') . '/', '/\.(xml|ser)$/'), '', $file);
            $result[] = array(
                'filename'    => $file,
                'filename_id' => $file
            );
        }
        return $result;
    }

    /**
     * Get package files from directory recursively
     *
     * @param string $dir
     * @param array &$result
     * @param bool $dirsFirst
     */
    protected function _collectRecursive($dir, &$result, $dirsFirst = true)
    {
        $_result = glob($dir . DS . '*');

        if (!is_array($_result)) {
            return;
        }

        if (!$dirsFirst) {
            // collect all the stuff recursively
            foreach ($_result as $item) {
                if (is_dir($item) && preg_match(self::$allowDirs, basename($item))) {
                    $this->_collectRecursive($item, $result, $dirsFirst);
                }
                elseif (is_file($item)
                    && preg_match(self::$allowFiles, basename($item))
                    && !preg_match(self::$disallowFiles, basename($item))) {
                        $result[] = $item;
                }
            }
        }
        else {
            // collect directories first
            $dirs  = array();
            $files = array();
            foreach ($_result as $item) {
                if (is_dir($item) && preg_match(self::$allowDirs, basename($item))) {
                    $dirs[] = $item;
                }
                elseif (is_file($item)
                    && preg_match(self::$allowFiles, basename($item))
                    && !preg_match(self::$disallowFiles, basename($item))) {
                        $files[] = $item;
                }
            }
            // search directories recursively
            foreach ($dirs as $item) {
                $this->_collectRecursive($item, $result, $dirsFirst);
            }
            // add files
            foreach ($files as $item) {
                $result[] = $item;
            }
        }
    }
}
