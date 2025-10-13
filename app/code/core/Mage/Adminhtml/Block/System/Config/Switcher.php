<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
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
            'url'      => $url->getUrl('*/*/*', ['section' => $section]),
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
                            'url'      => $url->getUrl('*/*/*', ['section' => $section, 'website' => $website->getCode()]),
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
                            'style'     => 'padding-left:32px;',
                        ];
                    }

                    $storeCode = $store->getCode();
                    $options['store_' . $storeCode] = [
                        'label'    => $store->getName(),
                        'url'      => $url->getUrl('*/*/*', ['section' => $section, 'website' => $website->getCode(), 'store' => $storeCode]),
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
