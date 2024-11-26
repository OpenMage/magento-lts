<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_System_ConfigController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/config';

    /**
     * Whether current section is allowed
     *
     * @var bool
     */
    protected $_isSectionAllowedFlag = true;

    /**
     * Controller pre-dispatch method
     * Check if current section is found and is allowed
     *
     * @return $this
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->getRequest()->getParam('section')) {
            $this->_isSectionAllowedFlag = $this->_isSectionAllowed($this->getRequest()->getParam('section'));
        }

        return $this;
    }

    /**
     * Index action
     *
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit configuration section
     */
    public function editAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        Mage::getSingleton('adminhtml/config_data')
            ->setSection($current)
            ->setWebsite($website)
            ->setStore($store);

        $configFields = Mage::getSingleton('adminhtml/config');

        $sections     = $configFields->getSections($current);
        $section      = $sections->$current;
        $hasChildren  = $configFields->hasChildren($section, $website, $store);
        if (!$hasChildren && $current) {
            $this->_redirect('*/*/', ['website' => $website, 'store' => $store]);
        }

        $this->loadLayout();

        $this->_setActiveMenu('system/config');
        $this->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo([$current]);

        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('System'),
            Mage::helper('adminhtml')->__('System'),
            $this->getUrl('*/system')
        );

        /** @var Mage_Adminhtml_Block_System_Config_Tabs $block */
        $block = $this->getLayout()->createBlock('adminhtml/system_config_tabs');
        $this->getLayout()->getBlock('left')
            ->append($block->initTabs());

        if ($this->_isSectionAllowedFlag) {
            /** @var Mage_Adminhtml_Block_System_Config_Edit $block */
            $block = $this->getLayout()->createBlock('adminhtml/system_config_edit');
            $this->_addContent($block->initForm());

            if (($current == 'carriers') && Mage::helper('core')->isModuleEnabled('Mage_Usa')) {
                $this->_addJs($this->getLayout()
                     ->createBlock('adminhtml/template')
                     ->setTemplate('system/shipping/ups.phtml'));
            }

            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/config/js.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate('system/shipping/applicable_country.phtml'));

            $this->renderLayout();
        }
    }

    /**
     * Save configuration
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /** @var Mage_Adminhtml_Model_Session $session */

        $groups = $this->getRequest()->getPost('groups', []);

        if (isset($_FILES['groups']['name']) && is_array($_FILES['groups']['name'])) {
            /*
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach ($_FILES['groups']['name'] as $groupName => $group) {
                if (is_array($group)) {
                    foreach ($group['fields'] as $fieldName => $field) {
                        if (!empty($field['value'])) {
                            $groups[$groupName]['fields'][$fieldName] = ['value' => $field['value']];
                        }
                    }
                }
            }
        }

        try {
            if (!$this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
                throw new Exception(Mage::helper('adminhtml')->__('This section is not allowed.'));
            }

            // custom save logic
            $this->_saveSection();
            $section = $this->getRequest()->getParam('section');
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::getSingleton('adminhtml/config_data')
                ->setSection($section)
                ->setWebsite($website)
                ->setStore($store)
                ->setGroupsSelector($groups)
                ->save();

            // reinit configuration
            Mage::getConfig()->reinit();
            Mage::dispatchEvent('admin_system_config_section_save_after', [
                'website' => $website,
                'store'   => $store,
                'section' => $section
            ]);
            Mage::app()->reinitStores();

            // website and store codes can be used in event implementation, so set them as well
            Mage::dispatchEvent(
                "admin_system_config_changed_section_{$section}",
                ['website' => $website, 'store' => $store]
            );

            if (!empty($groups)) {
                $session->addSuccess(Mage::helper('adminhtml')->__('The configuration has been saved.'));
            }
        } catch (Mage_Core_Exception $e) {
            foreach (explode("\n", $e->getMessage()) as $message) {
                $session->addError($message);
            }
        } catch (Exception $e) {
            $session->addException(
                $e,
                Mage::helper('adminhtml')->__('An error occurred while saving this configuration:') . ' '
                . $e->getMessage()
            );
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));

        $this->_redirect('*/*/edit', ['_current' => ['section', 'website', 'store']]);
    }

    /**
     *  Custom save logic for section
     */
    protected function _saveSection()
    {
        $method = '_save' . uc_words($this->getRequest()->getParam('section'), '');
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    /**
     *  Advanced save procedure
     */
    protected function _saveAdvanced()
    {
        Mage::app()->cleanCache(
            [
                'layout',
                Mage_Core_Model_Layout_Update::LAYOUT_GENERAL_CACHE_TAG
            ]
        );
    }

    /**
     * Save fieldset state through AJAX
     *
     */
    public function stateAction()
    {
        if ($this->getRequest()->getParam('isAjax') == 1
                    && $this->getRequest()->getParam('container') != ''
                        && $this->getRequest()->getParam('value') != ''
        ) {
            $configState = [
                $this->getRequest()->getParam('container') => $this->getRequest()->getParam('value')
            ];
            $this->_saveState($configState);
            $this->getResponse()->setBody('success');
        }
    }

    /**
     * Export shipping table rates in csv format
     *
     */
    public function exportTableratesAction()
    {
        $fileName   = 'tablerates.csv';
        /** @var Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid $gridBlock */
        $gridBlock  = $this->getLayout()->createBlock('adminhtml/shipping_carrier_tablerate_grid');
        $website    = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
        if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        } else {
            $conditionName = $website->getConfig('carriers/tablerate/condition_name');
        }
        $gridBlock->setWebsiteId($website->getId())->setConditionName($conditionName);
        $content    = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if specified section allowed in ACL
     *
     * Will forward to deniedAction(), if not allowed.
     *
     * @param string $section
     * @return bool
     */
    protected function _isSectionAllowed($section)
    {
        try {
            $session = Mage::getSingleton('admin/session');
            $resourceLookup = "admin/system/config/{$section}";
            if ($session->getData('acl') instanceof Mage_Admin_Model_Acl) {
                $resourceId = $session->getData('acl')->get($resourceLookup)->getResourceId();
                if (!$session->isAllowed($resourceId)) {
                    throw new Exception('');
                }
                return true;
            }
        } catch (Zend_Acl_Exception $e) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (Exception $e) {
            $this->deniedAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        return false;
    }

    /**
     * Save state of configuration field sets
     *
     * @param array $configState
     * @return bool
     */
    protected function _saveState($configState = [])
    {
        $adminUser = Mage::getSingleton('admin/session')->getUser();
        if (is_array($configState)) {
            $extra = $adminUser->getExtra();
            if (!is_array($extra)) {
                $extra = [];
            }
            if (!isset($extra['configState'])) {
                $extra['configState'] = [];
            }
            foreach ($configState as $fieldset => $state) {
                $extra['configState'][$fieldset] = $state;
            }
            $adminUser->saveExtra($extra);
        }

        return true;
    }
}
