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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Tabitems extends Mage_Adminhtml_Block_Template
{
    /**
     * Current active tab according preview action
     *
     * @var bool|string
     */
    private $activeTab = false;

    /**
     * Set preview tab items template
     */
    public function __construct()
    {
        parent::__construct();

        $deviceType = Mage::helper('xmlconnect')->getApplication()->getType();

        if ($deviceType == Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE) {
            $this->setTemplate('xmlconnect/edit/tab/design/preview/tab_items.phtml');
        } else {
            $this->setTemplate('xmlconnect/edit/tab/design/preview/tab_items_' . $deviceType . '.phtml');
        }
    }

    /**
     * Collect tab items array
     *
     * @return array
     */
    public function getTabItems()
    {
        $items = array();
        $model = Mage::helper('xmlconnect')->getApplication();
        $tabs = $model->getEnabledTabsArray();
        $tabLimit = (int) Mage::getStoreConfig('xmlconnect/devices/'.strtolower($model->getType()).'/tab_limit');
        $showedTabs = 0;
        foreach ($tabs as $tab) {
            if (++$showedTabs > $tabLimit) {
                break;
            }
            $items[$tab->action] = array(
                'label' => Mage::helper('xmlconnect')->getTabLabel($tab->action),
                'image' => $tab->image,
                'action' => $tab->action,
                'active' => strtolower($tab->action) == strtolower($this->activeTab),
            );
        }
        return $items;
    }

    /**
     * Check is exists a tab in a config array
     *
     * @param string $tabAction tab action name
     * @return bool
     */
    public function isItemExists($tabAction)
    {
        $tabs = $this->getTabItems();
        return (bool) isset($tabs[$tabAction]);
    }

   /**
    * Get preview images url
    *
    * @todo this method is a duplicate of a parent block method and have to be removed
    * @param string $name - file name
    * @return string
    */
    public function getPreviewImagesUrl($name = '')
    {
        return  Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $name);
    }

    /**
     * Get icon logo url
     *
     * @todo this method is a duplicate of a parent block method and have to be removed
     * @return string
     */
    public function getLogoUrl()
    {
        $configPath = 'conf/navigationBar/icon';
        if ($this->getData($configPath)) {
            return $this->getData($configPath);
        } else {
            return $this->getDesignPreviewImageUrl($this->getInterfaceImagesPaths($configPath));
        }
    }

   /**
    * Retrieve url for images in the skin folder
    *
    * @todo this method is a duplicate of a parent block method and have to be removed
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
     * @todo this method is a duplicate of a parent block method and have to be removed
     * @param string $path
     * @return array
     */
    public function getInterfaceImagesPaths($path)
    {
        $path = $this->_replaceConfig($path);
        return Mage::helper('xmlconnect/image')->getInterfaceImagesPaths($path);
    }

    /**
     * Converts Data path(conf/submision/zzzz) to config path (conf/native/submission/zzzzz)
     *
     * @todo this method is a duplicate of a parent block method and have to be removed
     * @param string $configPath
     * @return string
     */
    protected function _replaceConfig($configPath)
    {
        return $configPath = preg_replace('/^conf\/(.*)$/', 'conf/native/${1}', $configPath);
    }

    /**
     * Set active tab
     *
     * @param string $tab
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Tabitems
     */
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        return $this;
    }
}
