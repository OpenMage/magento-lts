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
 * config controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_ConfigController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->setFlag('index', 'no-preDispatch', true);
        return parent::_construct();
    }

    /**
     * Enter description here...
     *
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Enter description here...
     *
     */
    public function editAction()
    {
        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        $configFields = Mage::getSingleton('adminhtml/config');


        $sections     = $configFields->getSections($current);
        $section      = $sections->$current;
        $hasChildren  = $configFields->hasChildren($section, $website, $store);
        if (!$hasChildren && $current) {
            $this->_redirect('*/*/', array('website'=>$website, 'store'=>$store));
        }

        $this->loadLayout();

        $this->_setActiveMenu('system/config');

       $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'), $this->getUrl('*/system'));

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_tabs')->initTabs());

        if ($this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
            $this->_addContent($this->getLayout()->createBlock('adminhtml/system_config_edit')->initForm());

            $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('system/shipping/ups.phtml'));
            $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('system/config/js.phtml'));
            $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('system/shipping/applicable_country.phtml'));

            $this->renderLayout();
        }
    }

    /**
     * Enter description here...
     *
     */
    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */

        $groups = $this->getRequest()->getPost('groups');


        if (isset($_FILES['groups']['name']) && is_array($_FILES['groups']['name'])) {
            /**
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach($_FILES['groups']['name'] as $groupName => $group) {
                if (is_array($group)) {
                    foreach ($group['fields'] as $fieldName => $field) {
                        if (!empty($field['value'])) {
                            $groups[$groupName]['fields'][$fieldName] = array('value' => $field['value']);
                        }
                    }
                }
            }
        }

        try {
            Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG));
            if (!$this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
                throw new Exception(Mage::helper('adminhtml')->__('This section is not allowed.'));
            }

            // custom save logic
            $this->_saveSection();
            $section = $this->getRequest()->getParam('section');
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::getModel('adminhtml/config_data')
                ->setSection($section)
                ->setWebsite($website)
                ->setStore($store)
                ->setGroups($groups)
                ->save();

            // reinit configuration
            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();


            // website and store codes can be used in event implementation, so set them as well
            Mage::dispatchEvent("admin_system_config_changed_section_{$section}",
                array('website' => $website, 'store' => $store)
            );
            $session->addSuccess(Mage::helper('adminhtml')->__('Configuration successfully saved'));
        }
        catch (Mage_Core_Exception $e) {
            foreach(split("\n", $e->getMessage()) as $message) {
                $session->addError($message);
            }
        }
        catch (Exception $e) {
            $session->addException($e, Mage::helper('adminhtml')->__('Error while saving this configuration: '.$e->getMessage()));
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));

        $this->_redirect('*/*/edit', array('_current' => array('section', 'website', 'store')));
    }

    /**
     *  Custom save logic for section
     */
    protected function _saveSection ()
    {
        $method = '_save' . uc_words($this->getRequest()->getParam('section'), '');
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    /**
     *  Description goes here...
     */
    protected function _saveAdvanced ()
    {
        Mage::app()->cleanCache(
            array(
                'layout',
                Mage_Core_Model_Layout_Update::LAYOUT_GENERAL_CACHE_TAG
            )
        );
    }

    /**
     * action for ajax saving of fieldset state
     *
     */
    public function stateAction()
    {
        if ($this->getRequest()->getParam('isAjax') == 1
                    && $this->getRequest()->getParam('container') != ''
                        && $this->getRequest()->getParam('value') != '') {

            $configState = array(
                $this->getRequest()->getParam('container') => $this->getRequest()->getParam('value')
            );
            $this->_saveState($configState);
            $this->getResponse()->setBody('success');
        }
    }

    /**
     * Enter description here...
     *
     */
    public function exportTableratesAction()
    {
        $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));

        if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        } else {
            $conditionName = $websiteModel->getConfig('carriers/tablerate/condition_name');
        }

        $tableratesCollection = Mage::getResourceModel('shipping/carrier_tablerate_collection');
        /* @var $tableratesCollection Mage_Shipping_Model_Mysql4_Carrier_Tablerate_Collection */
        $tableratesCollection->setConditionFilter($conditionName);
        $tableratesCollection->setWebsiteFilter($websiteModel->getId());
        $tableratesCollection->load();

        $csv = '';

        $conditionName = Mage::getModel('shipping/carrier_tablerate')->getCode('condition_name_short', $conditionName);

        $csvHeader = array('"'.Mage::helper('adminhtml')->__('Country').'"', '"'.Mage::helper('adminhtml')->__('Region/State').'"', '"'.Mage::helper('adminhtml')->__('Zip/Postal Code').'"', '"'.$conditionName.'"', '"'.Mage::helper('adminhtml')->__('Shipping Price').'"');
        $csv .= implode(',', $csvHeader)."\n";

        foreach ($tableratesCollection->getItems() as $item) {
            if ($item->getData('dest_country') == '') {
                $country = '*';
            } else {
                $country = $item->getData('dest_country');
            }
            if ($item->getData('dest_region') == '') {
                $region = '*';
            } else {
                $region = $item->getData('dest_region');
            }
            if ($item->getData('dest_zip') == '') {
                $zip = '*';
            } else {
                $zip = $item->getData('dest_zip');
            }

            $csvData = array($country, $region, $zip, $item->getData('condition_value'), $item->getData('price'));
            foreach ($csvData as $cell) {
                $cell = '"'.str_replace('"', '""', $cell).'"';
            }
            $csv .= implode(',', $csvData)."\n";
        }

        header('Pragma: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=tablerates.csv");
        echo $csv;
        exit;
    }

    /**
     * Enter description here...
     *
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
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
            $resourceId = $session->getData('acl')->get($resourceLookup)->getResourceId();
            if (!$session->isAllowed($resourceId)) {
                throw new Exception('');
            }
            return true;
        }
        catch (Exception $e) {
            $this->_forward('denied');
            return false;
        }
    }

    /**
     * saving state of config field sets
     *
     * @param array $configState
     * @return bool
     */
    protected function _saveState($configState = array())
    {
        $adminUser = Mage::getSingleton('admin/session')->getUser();
        if (is_array($configState)) {
            $extra = $adminUser->getExtra();
            if (!is_array($extra)) {
                $extra = array();
            }
            if (!isset($extra['configState'])) {
                $extra['configState'] = array();
            }
            foreach ($configState as $fieldset => $state) {
                $extra['configState'][$fieldset] = $state;
            }
            $adminUser->saveExtra($extra);
        }

        return true;
    }
}
