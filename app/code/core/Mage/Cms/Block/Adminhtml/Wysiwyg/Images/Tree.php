<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory tree renderer for Cms Wysiwyg Images
 *
 * @category   Mage
 * @package    Mage_Cms
 */
class Mage_Cms_Block_Adminhtml_Wysiwyg_Images_Tree extends Mage_Adminhtml_Block_Template
{
    /**
     * Json tree builder
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getTreeJson()
    {
        $helper = Mage::helper('cms/wysiwyg_images');
        $collection = Mage::registry('storage')->getDirsCollection($helper->getCurrentPath());
        $jsonArray = [];
        foreach ($collection as $item) {
            $jsonArray[] = [
                'text'  => $helper->getShortFilename($item->getBasename()),
                'id'    => $helper->convertPathToId($item->getFilename()),
                'cls'   => 'folder'
            ];
        }
        return Zend_Json::encode($jsonArray);
    }

    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/treeJson');
    }

    /**
     * Root node name of tree
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->helper('cms')->__('Storage Root');
    }

    /**
     * Return tree node full path based on current path
     *
     * @return string
     */
    public function getTreeCurrentPath()
    {
        $treePath = '/root';
        if ($path = Mage::registry('storage')->getSession()->getCurrentPath()) {
            $helper = Mage::helper('cms/wysiwyg_images');
            $path = str_replace($helper->getStorageRoot(), '', $path);
            $relative = '';
            foreach (explode(DS, $path) as $dirName) {
                if ($dirName) {
                    $relative .= DS . $dirName;
                    $treePath .= '/' . $helper->idEncode($relative);
                }
            }
        }
        return $treePath;
    }
}