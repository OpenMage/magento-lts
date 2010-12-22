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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content extends Mage_Adminhtml_Block_Template
{
    /**
     * Prepare config data
     * Implement set "conf" data as magic method
     *
     * @param array $conf
     */
    public function setConf($conf)
    {
        if (!is_array($conf)) {
            $conf = array();
        }
        $tabs = isset($conf['tabBar']) && isset($conf['tabBar']['tabs']) ? $conf['tabBar']['tabs'] : false;
        if ($tabs !== false) {
            foreach ($tabs->getEnabledTabs() as $tab) {
                $conf['tabBar'][$tab->action]['label'] = $tab->label;
                $conf['tabBar'][$tab->action]['image'] =
                    Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $tab->image);
            }
        }
        $this->setData('conf', $conf);
    }

   /**
    * Get preview images url
    *
    * @param string $name - file name
    * @return string
    */
    public function getPreviewImagesUrl($name = '')
    {
        return  Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $name);
    }


   /**
    * Retrieve url for images in the skin folder
    *
    * @param string $name - path to file name relative to the skin dir
    * @return string
    */
    public function getDesignPreviewImageUrl($name)
    {
        return Mage::helper('xmlconnect/image')->getSkinImagesUrl('design_default/' . $name);
    }

    /**
     * Expose function getInterfaceImagesPaths from xmlconnect/images
     * Converts Data path(conf/submision/zzzz) to config path (conf/native/submission/zzzzz)
     *
     * @param string $path
     * @return array
     */
    public function getInterfaceImagesPaths($path)
    {
        $path = preg_replace('/^conf\/(.*)$/', 'conf/native/${1}', $path);
        return Mage::helper('xmlconnect/image')->getInterfaceImagesPaths($path);
    }

   /**
    * Get xmlconnect css url
    *
    * @param string $name - file name
    * @return string
    */
    public function getPreviewCssUrl($name = '')
    {
        return  Mage::getDesign()->getSkinUrl('xmlconnect/' . $name);
    }
}
