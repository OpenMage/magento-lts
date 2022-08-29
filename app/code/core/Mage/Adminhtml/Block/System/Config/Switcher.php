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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_System_Config_Switcher extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('system/config/switcher.phtml');
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /** @var Mage_Adminhtml_Model_System_Store $storeModel */

        $url = Mage::getModel('adminhtml/url');

        $options = [];
        $options['default'] = [
            'label'    => Mage::helper('adminhtml')->__('Default Config'),
            'url'      => $url->getUrl('*/*/*', ['section'=>$section]),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#ccc; font-weight:bold;',
        ];

        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $websiteShow = true;
                        $options['website_' . $website->getCode()] = [
                            'label'    => $website->getName(),
                            'url'      => $url->getUrl('*/*/*', ['section'=>$section, 'website'=>$website->getCode()]),
                            'selected' => !$curStore && $curWebsite == $website->getCode(),
                            'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
                        ];
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $options['group_' . $group->getId() . '_open'] = [
                            'is_group'  => true,
                            'is_close'  => false,
                            'label'     => $group->getName(),
                            'style'     => 'padding-left:32px;'
                        ];
                    }
                    $storeCode = $store->getCode();
                    $options['store_' . $storeCode] = [
                        'label'    => $store->getName(),
                        'url'      => $url->getUrl('*/*/*', ['section'=>$section, 'website'=>$website->getCode(), 'store'=>$storeCode]),
                        'selected' => $curStore == $storeCode,
                        'style'    => '',
                    ];
                }
                if ($groupShow) {
                    $options['group_' . $group->getId() . '_close'] = [
                        'is_group'  => true,
                        'is_close'  => true,
                    ];
                }
            }
        }

        return $options;
    }
}
