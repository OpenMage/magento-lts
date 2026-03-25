<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System configuration tabs block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Tabs extends Mage_Adminhtml_Block_Widget
{
    /**
     * @var array
     */
    protected $_tabs;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setId('system_config_tabs');
        $this->setTitle(Mage::helper('adminhtml')->__('Configuration'));
        $this->setTemplate('system/config/tabs.phtml');
    }

    /**
     * @param  Mage_Core_Model_Config_Element $a
     * @param  Mage_Core_Model_Config_Element $b
     * @return int
     */
    protected function _sort($a, $b)
    {
        return (int) $a->sort_order <=> (int) $b->sort_order;
    }

    public function initTabs()
    {
        $current = $this->getRequest()->getParam('section');
        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');

        $url = Mage::getModel('adminhtml/url');

        $configFields = Mage::getSingleton('adminhtml/config');
        $sections = $configFields->getSections($current);
        $tabs     = (array) $configFields->getTabs()->children();

        $sections = (array) $sections;

        usort($sections, [$this, '_sort']);
        usort($tabs, [$this, '_sort']);

        foreach ($tabs as $tab) {
            $helperName = $configFields->getAttributeModule($tab);
            $label = Mage::helper($helperName)->__((string) $tab->label);

            $this->addTab($tab->getName(), [
                'label' => $label,
                'class' => (string) $tab->class,
            ]);
        }

        foreach ($sections as $section) {
            Mage::dispatchEvent('adminhtml_block_system_config_init_tab_sections_before', ['section' => $section]);
            $hasChildren = $configFields->hasChildren($section, $websiteCode, $storeCode);

            //$code = $section->getPath();
            $code = $section->getName();

            $sectionAllowed = $this->checkSectionPermissions($code);
            if ((empty($current) && $sectionAllowed)) {
                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }

            $helperName = $configFields->getAttributeModule($section);

            $label = Mage::helper($helperName)->__((string) $section->label);

            if ($code == $current) {
                if (!$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store')) {
                    $this->_addBreadcrumb($label);
                } else {
                    $this->_addBreadcrumb($label, '', $url->getUrl('*/*/*', ['section' => $code]));
                }
            }

            if ($sectionAllowed && $hasChildren) {
                $this->addSection($code, (string) $section->tab, [
                    'class'     => (string) $section->class,
                    'label'     => $label,
                    'url'       => $url->getUrl('*/*/*', ['_current' => true, 'section' => $code]),
                ]);
            }

            if ($code == $current) {
                $this->setActiveTab($section->tab);
                $this->setActiveSection($code);
            }
        }

        /*
         * Set last sections
         */
        foreach ($this->getTabs() as $tab) {
            $sections = $tab->getSections();
            if ($sections) {
                $sections->getLastItem()->setIsLast(true);
            }
        }

        return $this;
    }

    /**
     * Add tab
     *
     * @param  string $code
     * @param  array  $config
     * @return $this
     */
    public function addTab($code, $config)
    {
        $tab = new Varien_Object($config);
        $tab->setId($code);
        $this->_tabs[$code] = $tab;
        return $this;
    }

    /**
     * Retrieve tab
     *
     * @param  string        $code
     * @return Varien_Object
     */
    public function getTab($code)
    {
        return $this->_tabs[$code] ?? null;
    }

    /**
     * @param  string $code
     * @param  string $tabCode
     * @param  array  $config
     * @return $this
     */
    public function addSection($code, $tabCode, $config)
    {
        if ($tab = $this->getTab($tabCode)) {
            if (!$tab->getSections()) {
                $tab->setSections(new Varien_Data_Collection());
            }

            $section = new Varien_Object($config);
            $section->setId($code);
            $tab->getSections()->addItem($section);
        }

        return $this;
    }

    /**
     * Get tabs
     *
     * @return array
     */
    public function getTabs()
    {
        return $this->_tabs;
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

    /**
     * @return string
     */
    public function getStoreButtonsHtml()
    {
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $html = '';

        if (!$curWebsite && !$curStore) {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('adminhtml')->__('New Website'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_website/new') . "'",
                'class'     => 'add',
            ])->toHtml();
        } elseif (!$curStore) {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('adminhtml')->__('Edit Website'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_website/edit', ['website' => $curWebsite]) . "'",
            ])->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('adminhtml')->__('New Store View'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_store/new', ['website' => $curWebsite]) . "'",
                'class'     => 'add',
            ])->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('adminhtml')->__('Delete Website'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_website/delete', ['website' => $curWebsite]) . "'",
                'class'     => 'delete',
            ])->toHtml();
        } else {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('adminhtml')->__('Edit Store View'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_store/edit', ['store' => $curStore]) . "'",
            ])->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('adminhtml')->__('Delete Store View'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_store/delete', ['store' => $curStore]) . "'",
                'class'     => 'delete',
            ])->toHtml();
        }

        return $html;
    }

    /**
     * @param  string $code
     * @return bool
     */
    public function checkSectionPermissions($code = null)
    {
        static $permissions;

        if (!$code || trim($code) == '') {
            return false;
        }

        if (!$permissions) {
            $permissions = Mage::getSingleton('admin/session');
        }

        $showTab = false;
        if ($permissions->isAllowed('system/config/' . $code)) {
            return true;
        }

        return $showTab;
    }
}
