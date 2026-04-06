<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * admin customer left menu
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Dwstree extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        #$this->setTemplate('widget/tabs.phtml');
        $this->setId('system_config_dwstree');
        $this->setDestElementId('system_config_form');
    }

    public function initTabs()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $websitesConfig = Mage::getConfig()->getNode('websites');
        $storesConfig = Mage::getConfig()->getNode('stores');

        $this->addTab('default', [
            'label'  => Mage::helper('adminhtml')->__('Default Config'),
            'url'    => $this->getUrl('*/*/*', ['section' => $section]),
            'class' => 'default',
        ]);

        foreach ($websitesConfig->children() as $wCode => $wConfig) {
            $wName = (string) $wConfig->descend('system/website/name');
            $wUrl = $this->getUrl('*/*/*', ['section' => $section, 'website' => $wCode]);
            $this->addTab('website_' . $wCode, [
                'label' => $wName,
                'url'   => $wUrl,
                'class' => 'website',
            ]);
            if ($curWebsite === $wCode) {
                if ($curStore) {
                    $this->_addBreadcrumb($wName, '', $wUrl);
                } else {
                    $this->_addBreadcrumb($wName);
                }
            }

            foreach ($wConfig->descend('system/stores')->children() as $sCode => $sId) {
                $sName = (string) $storesConfig->descend($sCode . '/system/store/name');
                $this->addTab('store_' . $sCode, [
                    'label' => $sName,
                    'url'   => $this->getUrl('*/*/*', ['section' => $section, 'website' => $wCode, 'store' => $sCode]),
                    'class' => 'store',
                ]);
                if ($curStore === $sCode) {
                    $this->_addBreadcrumb($sName);
                }
            }
        }

        if ($curStore) {
            $this->setActiveTab('store_' . $curStore);
        } elseif ($curWebsite) {
            $this->setActiveTab('website_' . $curWebsite);
        } else {
            $this->setActiveTab('default');
        }

        return $this;
    }
}
